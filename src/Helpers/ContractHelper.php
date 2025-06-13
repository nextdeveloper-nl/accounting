<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Services\ContractsService;

class ContractHelper
{
    public const FIXED_DISCOUNT = 'fixed-discount';

    public const FIXED_PRICE = 'fixed-price';

    public const SIX_MONTHS = '6months';

    public const ONE_YEAR = '1year';

    public const TWO_YEAR = '2year';

    public const THREE_YEAR = '3year';

    public const FOUR_YEAR = '4year';

    public const FIVE_YEAR = '5year';

    public static function getContractById($contractId) :Contracts {
        if(Str::isUuid($contractId))
            return Contracts::where('uuid', $contractId)->first();

        return Contracts::where('id', $contractId)->first();
    }

    public static function createNewContractForCrmAccount(
        \NextDeveloper\CRM\Database\Models\Accounts $account,
        $from = null,
        $lenght = null
    ) {
        return self::createNewContract(
            AccountingHelper::getAccountFromCrmAccount($account)
        );
    }

    public static function createNewContract(Accounts $accountingAccount, $from = null, $lenght = null)
    {
        if(!$from)
            $from = now();

        if(!$lenght)
            $lenght = self::THREE_YEAR;

        $termStart = $from;
        $termEnd = null;

        switch ($lenght) {
            case self::SIX_MONTHS:
                $termEnd = $termStart->copy()->addMonths(6);
                break;
            case self::ONE_YEAR:
                $termEnd = $termStart->copy()->addMonths(12);
                break;
            case self::TWO_YEAR:
                $termEnd = $termStart->copy()->addMonths(24);
                break;
            case self::THREE_YEAR;
                $termEnd = $termStart->copy()->addMonths(36);
                break;
            case self::FOUR_YEAR;
                $termEnd = $termStart->copy()->addMonths(48);
                break;
            case self::FIVE_YEAR:
                $termEnd = $termStart->copy()->addMonths(60);
                break;
        }

        return ContractsService::create([
            'name'          =>  'Initial draft contract',
            'contract_type' =>  self::FIXED_DISCOUNT,
            'term_starts'   =>  $termStart,
            'term_end'      =>  $termEnd,
            'is_approved'   =>  false,
            'accounting_account_id' =>  $accountingAccount->id
        ]);
    }

    public function setLenght($contract = null, $lenght = self::THREE_YEAR) {
        if(!$contract) {
            return null;
        }

        $termEnd = null;

        switch ($lenght) {
            case self::SIX_MONTHS:
                $termEnd = $contract->term_starts->copy()->addMonths(6);
                break;
            case self::ONE_YEAR:
                $termEnd = $contract->term_starts->copy()->addMonths(12);
                break;
            case self::TWO_YEAR:
                $termEnd = $contract->term_starts->copy()->addMonths(24);
                break;
            case self::THREE_YEAR;
                $termEnd = $contract->term_starts->copy()->addMonths(36);
                break;
            case self::FOUR_YEAR;
                $termEnd = $contract->term_starts->copy()->addMonths(48);
                break;
            case self::FIVE_YEAR:
                $termEnd = $contract->term_starts->copy()->addMonths(60);
                break;
        }

        $contract->update([
            'term_ends' =>  $termEnd
        ]);

        $contract = $contract->fresh();

        return $contract;
    }
}
