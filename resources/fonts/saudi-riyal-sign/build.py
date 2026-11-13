"""Build saudi-riyal-sign.woff2: one glyph for U+20C1 SAUDI RIYAL SIGN.

Source outline: Saudi_Riyal_Symbol-2.svg, the official symbol file of the
Saudi Central Bank (SAMA). See README.md for the source URL.

The shape is not changed: the outline gets one uniform scale (and the flip
from SVG y-down to font y-up). It is as tall as the figures of Figtree
(700/1000 em) and sits on the baseline (SAMA usage rule "Alignment").
Side bearings are 50 units. The line metrics (800/200) do not make a line
taller than the text font does.

Run: pip install fonttools brotli && python build.py Saudi_Riyal_Symbol-2.svg
"""
import sys
from fontTools.fontBuilder import FontBuilder
from fontTools.pens.areaPen import AreaPen
from fontTools.pens.boundsPen import BoundsPen
from fontTools.pens.cu2quPen import Cu2QuPen
from fontTools.pens.recordingPen import RecordingPen
from fontTools.pens.reverseContourPen import ReverseContourPen
from fontTools.pens.transformPen import TransformPen
from fontTools.pens.ttGlyphPen import TTGlyphPen
from fontTools.svgLib.path import SVGPath

UPM, HEIGHT, SIDE = 1000, 700, 50
FAMILY = "CornerTill Riyal Sign"

svg = RecordingPen()
SVGPath(sys.argv[1]).draw(svg)
bp = BoundsPen(None)
svg.replay(bp)
x0, y0, x1, y1 = bp.bounds
s = HEIGHT / (y1 - y0)

# Uniform scale s; y is flipped (SVG y goes down), the bottom of the symbol is on the baseline.
glyph = RecordingPen()
svg.replay(TransformPen(glyph, (s, 0, 0, -s, SIDE - x0 * s, y1 * s)))

# TrueType outer contours go clockwise (negative area in fontTools).
area = AreaPen()
glyph.replay(area)
pen = TTGlyphPen(None)
target = Cu2QuPen(pen, max_err=0.25)  # glyf needs quadratic curves
glyph.replay(ReverseContourPen(target) if area.value > 0 else target)
riyal = pen.glyph()
width = round((x1 - x0) * s) + 2 * SIDE

fb = FontBuilder(UPM, isTTF=True)
fb.setupGlyphOrder([".notdef", "uni20C1"])
fb.setupCharacterMap({0x20C1: "uni20C1"})
fb.setupGlyf({".notdef": TTGlyphPen(None).glyph(), "uni20C1": riyal})
fb.setupHorizontalMetrics({".notdef": (500, 0), "uni20C1": (width, SIDE)})
fb.setupHorizontalHeader(ascent=800, descent=-200)
fb.setupNameTable({
    "familyName": FAMILY,
    "styleName": "Regular",
    "version": "Version 2.000",
    "copyright": "Saudi Riyal symbol: Saudi Central Bank (SAMA). Shape unchanged.",
})
fb.setupOS2(version=4, sTypoAscender=800, sTypoDescender=-200, sTypoLineGap=0,
            usWinAscent=800, usWinDescent=200, sCapHeight=700, sxHeight=500,
            fsSelection=0x40 | 0x80, usFirstCharIndex=0x20C1, usLastCharIndex=0x20C1)
fb.setupPost()
fb.font.flavor = "woff2"
fb.save("saudi-riyal-sign.woff2")
print("bounds", bp.bounds, "scale", round(s, 5), "width", width, "area sign", area.value > 0)
