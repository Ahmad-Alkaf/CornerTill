// The global route() function (Ziggy). It comes from the ziggy-js package in
// the bundle, which the browser keeps for a year (hashed file name), and not
// inline in each HTML response (about 20 KB). ziggy-js reads the routes from
// the <script id="ziggy-routes-json"> tag of @routes(json: true) in
// resources/views/app.blade.php. app.tsx imports this file first, so that
// route() exists before other code runs.
import { route } from "ziggy-js";

globalThis.route = route;
