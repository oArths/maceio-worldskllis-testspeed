<?php
function data($data, $status = 200)
{
    return response()->json($data, $status);
}
