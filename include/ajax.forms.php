<?php

require_once(INCLUDE_DIR . 'class.topic.php');
require_once(INCLUDE_DIR . 'class.dynamic_forms.php');

class DynamicFormsAjaxAPI extends AjaxController {
    function getForm($form_id) {
        $form = DynamicForm::lookup($form_id);
        if (!$form) return;

        foreach ($form->getFields() as $field) {
            $field->render();
        }
    }

    function getFormsForHelpTopic($topic_id, $client=false) {
        $topic = Topic::lookup($topic_id);
        if ($topic->ht['form_id']
                && ($form = DynamicForm::lookup($topic->ht['form_id'])))
            $form->render(!$client);
    }

    function getClientFormsForHelpTopic($topic_id) {
        return $this->getFormsForHelpTopic($topic_id, true);
    }

    function getFieldConfiguration($field_id) {
        $field = DynamicFormField::lookup($field_id);
        include(STAFFINC_DIR . 'templates/dynamic-field-config.tmpl.php');
    }

    function saveFieldConfiguration($field_id) {
        $field = DynamicFormField::lookup($field_id);
        if (!$field->setConfiguration())
            include(STAFFINC_DIR . 'templates/dynamic-field-config.tmpl.php');
        else
            $field->save();
    }

    function deleteAnswer($entry_id, $field_id) {
        global $thisstaff;

        if (!$thisstaff)
            Http::response(403, 'Login required');

        $ent = DynamicFormEntryAnswer::lookup(array(
            'entry_id'=>$entry_id, 'field_id'=>$field_id));
        if (!$ent)
            Http::response(404, 'Answer not found');

        $ent->delete();
    }

    function getListItemProperties($item_id) {
        if (!($item = DynamicListItem::lookup($item_id)))
            Http::response(404, 'No such list item');

        include(STAFFINC_DIR . 'templates/list-item-properties.tmpl.php');
    }

    function saveListItemProperties($item_id) {
        if (!($item = DynamicListItem::lookup($item_id)))
            Http::response(404, 'No such list item');

        if (!$item->setConfiguration())
            include(STAFFINC_DIR . 'templates/list-item-properties.tmpl.php');
        else
            $item->save();
    }
}
?>
