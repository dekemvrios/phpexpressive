<?php

namespace Solis\Expressive\Classes\Illuminate\Util;

use Solis\Expressive\Contracts\ExpressiveContract;
use Solis\Breaker\TException;
/**
 * Class Actions
 *
 * @package Solis\Expressive\Classes\Illuminate\Utils
 */
class Actions
{

    /**
     * @param ExpressiveContract $model
     * @param string             $whenAction
     * @param string             $doThing
     *
     * @return ExpressiveContract
     * @throws TException
     */
    public static function doThingWhenDatabaseAction($model, $whenAction, $doThing)
    {
        $actions = $model::$schema->getActions();
        if(empty($actions)){
            return $model;
        }

        $whenActionObj = $actions->{'get' . ucfirst($whenAction)}();
        if(empty($whenActionObj)){
            return $model;
        }

        $doThingObj = $whenActionObj->{'get' . ucfirst($doThing)}();
        if (empty($doThingObj)) {
            return $model;
        }

        foreach ($doThingObj as $action) {
            $param = !is_array($action->getParams()) ? [$action->getParams()] : $action->getParams();
            if (in_array(
                'this',
                $param
            )) {

                $param[array_search(
                    'this',
                    $param
                )] = $model;
            }

            $result = call_user_func_array(
                [$action->getClass(), $action->getFunction()],
                $param
            );

            if (empty($result) || !is_bool($result)) {
                throw new TException(
                    __CLASS__,
                    __METHOD__,
                    "error executing {$whenAction} {$doThing} [{$action->getFunction()}] action for class " . get_class($model),
                    500
                );
            }
        }

        return $model;
    }
}