<?php

namespace Payment\Contract;

interface Contract
{
    public static function paymentReadyOk(int $user_id);
}
