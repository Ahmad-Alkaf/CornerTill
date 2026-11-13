<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Bill\StoreBillRequest;
use Illuminate\Validation\ValidationException;

class BillController extends Controller
{
    /**
     * Display a listing of the resource (Bills page).
     */
    public function index(Request $request)
    {
        $bills = $request->user()->business->bills()->latest()->with([
            'createdBy' => function ($query) {
                $query->withTrashed();
            },
            'bill_details.product'
        ])
            ->filter($request->only('search', 'product'))
            ->paginate(15)->appends($request->all());

        return Inertia::render('Authenticated/Bills/index', [
            'bills' => $bills,
            'filter' => $request->only('search'),
        ]);
    }

    /**Bill show can be accessed by anyone for QR link on the bill paper (Bill/Invoice page) */
    protected function show(Bill $bill)
    {
        Gate::authorize('show', $bill);
        $bill = $bill->load([
            'bill_details.product',
            'business',
            'createdBy' => function ($query) {
                $query->withTrashed();
            }
        ]);

        return Inertia::render('Authenticated/Bill/index', [
            'bill' => $bill,
        ]);
    }

    /**Bill edit will be displayed in Checkout Page */
    protected function edit(Request $request, Bill $bill)
    {
        Gate::authorize('edit', $bill);
        $bill = $bill->load('bill_details.product');
        // The cart shows the values of the original sale (store() keeps them).
        // Only the copy for this page changes; the products are not saved.
        foreach ($bill->bill_details as $detail) {
            $detail->product->price = $detail->price;
            $detail->product->name = $detail->productName ?? $detail->product->name;
        }
        return $this->checkoutPage($request, $bill);
    }

    //AKA Checkout
    protected function create(Request $request)
    {
        return $this->checkoutPage($request);
    }

    private function checkoutPage(Request $request, Bill $bill = null)
    {

        $products = $request->user()->business->products()->latest()
            ->filter($request->only('search', 'barcode'))
            ->paginate(30)->appends($request->all());
        $createdUpdatedBillId = Session::get('createdUpdatedBillId');
        return Inertia::render('Authenticated/Checkout/index', [
            'products' => $products,
            'filter' => $request->only('search', 'barcode'),
            'bill' => $bill,
            'createdUpdatedBillId' => $createdUpdatedBillId,
        ]);
    }

    /**
     * Store or Update.
     *
     * Update is "store a new bill, then delete the old one" (the bill gets a new
     * ID), because the bill details of a bill can change. A bill stores the values
     * of the sale: the tax rate and currency of the business, and the unit price
     * and name of each product. They do not change later when the business or a
     * product changes. An update keeps the tax rate, the currency, the date of the
     * sale, and the unit price and name of each product that was already in the
     * bill. Only a product that the update adds takes its current values.
     */
    public function store(StoreBillRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();
        $business = $user->business;

        $oldBill = null;
        if ($request->get('id')) {
            $oldBill = Bill::with('bill_details')->findOrFail($request->get('id'));
            Gate::authorize('update', $oldBill);
        } else Gate::authorize('store', Bill::class);

        // Only the products of this business can be sold.
        $productIds = collect($validated['bill_details'])->pluck('product_id')->unique();
        $products = $business->products()->whereIn('id', $productIds)->get()->keyBy('id');
        if ($products->count() !== $productIds->count())
            throw ValidationException::withMessages(['serverError' => __('A product in this bill does not exist. Remove it from the bill and try again.')]);

        $oldLines = $oldBill ? $oldBill->bill_details->keyBy('product_id') : collect();
        $billDetails = [];
        foreach ($validated['bill_details'] as $line) {
            $product = $products[$line['product_id']];
            $oldLine = $oldLines->get($product->id);
            $billDetails[] = [
                'product_id' => $product->id,
                'quantity' => $line['quantity'],
                'price' => $oldLine ? $oldLine->price : $product->price,
                'productName' => $oldLine ? ($oldLine->productName ?? $product->name) : $product->name,
            ];
        }

        $createdBill = DB::transaction(function () use ($validated, $user, $business, $oldBill, $billDetails) {
            $bill = new Bill([
                'cashReceived' => $validated['cashReceived'] ?? null,
                'taxPercent' => $oldBill ? $oldBill->taxPercent : $business->taxPercent,
                'currency' => $oldBill ? $oldBill->currency : $business->currency,
            ]);
            $bill->business_id = $user->business_id;
            $bill->createdBy_id = $user->id;
            if ($oldBill)
                $bill->created_at = $oldBill->created_at; // the date of the sale
            $bill->save();
            $bill->bill_details()->createMany($billDetails);

            // Deleting the old bill gives its quantities back to the stock (BillObserver).
            $oldBill?->delete();
            return $bill;
        });

        return to_route('bill.create')->with([
            'success' => $oldBill ? 'Successfully updated' : 'Successfully created',
            'createdUpdatedBillId' => $createdBill->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        Gate::authorize('delete', $bill);

        $bill->delete();
        return redirect()->back()->with('success', __('Successfully deleted'));
    }
}
