<?php

use MoesifFilter;

class MyCustomFilter extends MoesifFilter {
    /**
     * Optional hook to link API calls to users
     */
    public function identifyUserId($request, $response){
        $user = $this->getContext()->getUser();
        return $user->getAttribute("user_id");
    }
}
