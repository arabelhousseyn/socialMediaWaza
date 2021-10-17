<?php

namespace App\Traits;

use App\Models\{
  User,
  Group
};

trait middlewares{
    // check if you're allowed to see this group
    public function checkIfEligible($age,$user_gender,$group_id)
    {
        $group = Group::find($group_id);

        if($group->gender == null)
            {
                // adding this condition because php doesn't split between split recognition 0 && null
                if(strval($group->gender) != 0)
                {
                    return true;
                }
            }else if($age >= $group->minAge && $age <= $group->maxAge)
                       {
                        if($group->gender == 2)
                        {
                            return true;
                        }else{
                           if($group->gender == $user_gender)
                           {
                            return true;
                           } 
                        }
                       }
                       return false;
    }
}