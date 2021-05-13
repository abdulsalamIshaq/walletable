<?php

namespace Walletable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Walletable\Traits\PrimaryUuid;

class WalletTransaction extends Model
{
    use HasFactory, PrimaryUuid;
}
