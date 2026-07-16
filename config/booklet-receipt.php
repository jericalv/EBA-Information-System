<?php

/*
|--------------------------------------------------------------------------
| Uniform sales booklet receipt — overlay layout
|--------------------------------------------------------------------------
| The physical booklet leaf is 90 mm wide x 130 mm tall (Shopee spec
| "S:130*90mm"). The PDF prints ONLY the values; the labels, table lines
| and receipt number are already on the pre-printed paper. Every
| coordinate below is in millimetres measured from the TOP-LEFT corner
| of the leaf.
|
| For each field, `y` is the printed blank LINE the value must sit on
| (text is rendered just above it) and `x` is where the writable area
| starts. Current values are estimated from a photo of the booklet —
| fine-tune them against the on-screen preview (?preview=1), then dial in
| the physical printer with the two offsets (or ?dx= / ?dy= in mm).
*/

return [

    // Global nudge applied to every field, in mm. Positive x moves the
    // whole print right, positive y moves it down. These absorb the
    // printer's paper-feed origin error — calibrate once with a test
    // print and never touch the per-field numbers again.
    'offset_x' => 0.0,
    'offset_y' => 0.0,

    'font_size' => 8, // pt

    // DATE ________ (top right)
    'date' => ['x' => 64.9, 'y' => 5.3, 'w' => 17.0],

    // SOLD TO ________ (left blank unless a name is provided at print time)
    'sold_to' => ['x' => 21.8, 'y' => 11.1, 'w' => 60.0],

    // ADDRESS ________
    'address' => ['x' => 21.3, 'y' => 16.0, 'w' => 60.5],

    // Item table. `x` is the column's left edge, `w` its width.
    'columns' => [
        'qty'      => ['x' => 6.3,  'w' => 7.6,  'align' => 'center'],
        'unit'     => ['x' => 13.8, 'w' => 8.7,  'align' => 'center'],
        'articles' => ['x' => 23.0, 'w' => 31.3, 'align' => 'left'],
        'price'    => ['x' => 54.7, 'w' => 11.4, 'align' => 'right'],
        'amount'   => ['x' => 66.5, 'w' => 14.9, 'align' => 'right'],
    ],

    // `first_y` is the bottom line of the FIRST item row; each following
    // row line sits `pitch` mm lower. The booklet has `max` ruled rows.
    'rows' => ['first_y' => 29.4, 'pitch' => 5.7, 'max' => 14],

    // TOTAL ________ (value goes in the amount column, on the total line)
    'total' => ['x' => 66.5, 'y' => 110.8, 'w' => 14.9],
];
