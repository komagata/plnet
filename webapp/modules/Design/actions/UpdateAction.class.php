<?php
class UpdateAction extends Action
{
    var $layout = 'Admin';

    function initialize (&$controller, &$request, &$user)
    {
        $this->attrs['title'] = "Plnet &gt; ".msg('setting')." &gt; ".msg('design');

        $request->setAttribute('member', $user->getAttribute('member', GLU_NS));
        $design = DB_DataObject::factory('design');
        $design->find();
        $designs = array();
        while ($design->fetch()) {
            $designs[] = $design;
        }
        $request->setAttribute('designs', $designs);

        $m = $user->getAttribute('member', GLU_NS);
        $member = DB_DataObject::factory('member');
        $member->get($m->id);
        $request->setAttribute('member', $member);

        $ct = DB_DataObject::factory('custom_template');
        $ct->get('member_id', $m->id);
        $request->setAttribute('custom_template', $ct);

        // default css
        $default_css = file_get_contents(PLNET_DEFAULT_CSS);
        $request->setAttribute('default_css', $default_css);

        return true;
    }

    function isSecure()
    {
        return true;
    }

    function getRequestMethods()
    {
        return REQ_POST;
    }

    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);
        $user->setAttribute('member', $member, GLU_NS);

        $custom = $request->hasParameter('custom_design_submit') 
            ? $request->getParameter('custom_design_submit') : null;
        if ($custom) {
            $ct = DB_DataObject::factory('custom_template');
            $ct->member_id = $member->id;
            if ($ct->count() > 0) {
                $ct->get('member_id', $member->id);
                $ct->template = $request->getParameter('css');
                $ct->update();
            } else {
                $ct->member_id = $member->id;
                $ct->template = $request->getParameter('css');
                $ct->insert();
            }
            Controller::redirect(SCRIPT_PATH . 'setting/design/changed_custom');
        } else {
            $member->design_id = $request->getParameter('design_id');
            $member->update();
            Controller::redirect(SCRIPT_PATH . 'setting/design/changed');
        }
        return VIEW_NONE;
    }
}
?>
