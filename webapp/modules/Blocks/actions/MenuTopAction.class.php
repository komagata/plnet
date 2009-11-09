<?php
class MenuTopAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = $user->hasAttribute('member', GLU_NS) 
            ? $user->getAttribute('member', GLU_NS) : null;
        $member->photo_url = $member->photo
            ? '/photo.php?member_id='.$member->id : PLNET_DEFAULT_PHOTO;

        $request->setAttribute('member', $member);
        return VIEW_SUCCESS;
    }
}
?>
