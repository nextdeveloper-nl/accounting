<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractAccountsTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class AccountsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AccountsTransformer extends AbstractAccountsTransformer
{

    /**
     * @param Accounts $model
     *
     * @return array
     */
    public function transform(Accounts $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Accounts', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Accounts', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
