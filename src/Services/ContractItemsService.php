<?php

namespace NextDeveloper\Accounting\Services;

use App\Helpers\ObjectHelper;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractContractItemsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * This class is responsible from managing the data for ContractItems
 *
 * Class ContractItemsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class ContractItemsService extends AbstractContractItemsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    public static function create($data)
    {
        $data['object_id'] = ObjectHelper::getObject($data['object_type'], $data['object_id'])->id;

        if(Str::isUuid($data['accounting_contract_id']))
            $data['accounting_contract_id'] = Contracts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $data['accounting_contract_id'])->first()->id;
        else
            $data['accounting_contract_id'] = Contracts::withoutGlobalScope(AuthorizationScope::class)->where('id', $data['accounting_contract_id'])->first()->id;

        if(Str::isUuid($data['accounting_account_id']))
            $data['accounting_account_id'] = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $data['accounting_account_id'])->first()->id;
        else
            $data['accounting_account_id'] = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('id', $data['accounting_account_id'])->first()->id;

        return parent::create($data);
    }
}
