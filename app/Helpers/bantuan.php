<?php

function rupiah($nominal) {
    return "Rp " . number_format($nominal, 0, ',', '.');
}

function dolar($nominal) {
    return "USD " . number_format($nominal, 2);
}
