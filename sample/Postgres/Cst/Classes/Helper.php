<?php

namespace Sample\Postgres\Cst\Classes;

/**
 * Class Helper
 *
 * @package Sample\Postgres\Cst\Classes
 */
class Helper
{

    /**
     * @param $param1
     * @param $param2
     * @param $param3
     *
     * @return boolean
     */
    public function testeDeFuncaoInsert1(
        $param1,
        $param2,
        $param3
    ) {
        //var_dump([$param1, $param2, $param3]);

        return true;
    }

    /**
     * @param $param1
     *
     * @return boolean
     */
    public function testeDeFuncaoInsert2(
        $param1
    ) {
        //var_dump([$param1, $param2, $param3]);

        return true;
    }

    /**
     * @param $param1
     * @param $param2
     *
     * @return boolean
     */
    public function testeDeFuncaoUpdate1(
        $param1,
        $param2
    ) {
        //var_dump([$param1, $param2, $param3]);

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function testeDeFuncaoDelete1(
    ) {
        //var_dump([$param1, $param2, $param3]);

        return true;
    }

    /**
     * @param $param1
     * @param $param2
     *
     * @return boolean
     */
    public function testeDeFuncaoDelete2(
        $param1,
        $param2
    ) {
        //var_dump([$param1, $param2, $param3]);

        return true;
    }

}