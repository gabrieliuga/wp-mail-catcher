<?php

namespace MailCatcher;

use MailCatcher\Models\Logs;
use MailCatcher\Models\Mail;

class MailAdminTable extends WP_List_Table
{
    function __construct()
	{
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'log',     //singular name of the listed records
            'plural'    => 'logs',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ));
    }

    function column_default($item, $column_name)
	{
        switch($column_name){
            case 'time':
            case 'email_to':
            case 'subject':
            case 'status':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_time($item)
	{
        //Build row actions
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('Delete', 'MailCatcher') . '</a>',
                'mail-catcher',
                'delete',
                $item['id']),
            'resend' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('Resend', 'MailCatcher') . '</a>',
                'mail-catcher',
                'resend',
                $item['id']),
            'export' => sprintf('<a href="?page=%s&action=%s&id=%s">' . __('Export', 'MailCatcher') . '</a>',
                'mail-catcher',
                'export',
                $item['id']),
        );

		return sprintf('%1$s %2$s',
			$item['time'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
	{
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'id',
            $item['id']
        );
    }

    function column_more_info($item)
	{
        return '<a href="#" class="button button-secondary" data-toggle="modal" data-target="#' . $item['id'] . '">More Info</a>';
    }

    function get_columns()
	{
        $columns = array(
            'cb'       => '<input type="checkbox" />', //Render a checkbox instead of text
            'time'  => __('Sent', 'MailCatcher'),
            'email_to'  => __('To', 'MailCatcher'),
            'subject'  => __('Subject', 'MailCatcher'),
            'status'  => __('Status', 'MailCatcher'),
            'more_info' => ''
        );

        return $columns;
    }

    function column_status($item)
	{
        if ($item['status'] == true) {
            return '<span class="status">' . __('Success', 'MailCatcher') . '</span>';
        }

        return '<span class="status" data-error="' . $item['error'] . '">' . __('Failed', 'MailCatcher') . '</span>';
    }

    function get_sortable_columns()
	{
        $sortable_columns = array(
            'time'  => array('time', false),
            'email_to'  => array('email_to', false),
            'subject'  => array('subject', false),
            'status'  => array('status', false),
        );

        return $sortable_columns;
    }

    function get_bulk_actions()
	{
        $actions = array(
            'delete'    => __('Delete', 'MailCatcher'),
            'resend' => __('Resend', 'MailCatcher'),
            'export' => __('Export', 'MailCatcher')
        );

        return $actions;
    }

    function process_bulk_action()
	{
        //Detect when a bulk action is being triggered...
        switch ($this->current_action()) {
            case 'delete' :
                Logs::delete($_REQUEST['id']);
            break;
            case 'export' :
//                Mail::export($_REQUEST['id']);
            break;
            case 'resend' :
                Mail::resend($_REQUEST['id']);
            break;
        }
    }

    function prepare_items()
	{
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $this->items = Logs::get(array(
			'paged' => $this->get_pagenum()
		));
        $total_items = Logs::getTotalAmount();

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => Logs::getTotalPages()//ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ));
    }
}
