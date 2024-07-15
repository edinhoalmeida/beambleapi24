<?php

namespace Shipping\Contract;

interface Contract
{
    public static function shipping_details(int $beamer_id, int $client_id);
}
