/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        var deleteButton;

        $(document).on('click', '.btn-confirm',function(e) {
            e.preventDefault();

            deleteButton = $(this);

            if (deleteButton.is("a")) {
                $('#confirmation-modal #confirmation-modal-confirm').attr('href', deleteButton.attr('href'));
            }

            $('#confirmation-modal').modal('show');
        });

        $('#confirmation-modal #confirmation-modal-confirm').click(function(e) {
            if (deleteButton.is("button")) {
                e.preventDefault();
                deleteButton.closest('form').submit();
            }
        });

        // New confirmation dialog
        var $actionButton, titleText, bodyText, actionText;
        var $modal = $('#backend-confirmation-modal');
        var $modalTitle = $modal.find('.modal-title');
        var $modalBody = $modal.find('.modal-body');
        var $modalAction = $('#backend-confirmation-modal #backend-confirmation-modal-confirm');

        $(document).on('click', '[data-confirm]',function(e) {
            e.preventDefault();
            $actionButton = $(this);

            if ($actionButton.is("a")) {
                $modalAction.attr('href', $actionButton.attr('href'));
            }

            titleText = $modalTitle.text();
            bodyText = $modalBody.text();
            actionText = $modalAction.find('span').text();

            if ($actionButton.data('title') !== undefined
                && $actionButton.data('title') !== '')
                $modalTitle.text($actionButton.data('title'));
            if ($actionButton.data('message') !== undefined
                && $actionButton.data('message') !== '')
                $modalBody.text($actionButton.data('message'));
            if ($actionButton.data('confirm') !== '')
                $modalAction.find('span').text($actionButton.data('confirm'));
            $modal.modal('show');
        });
        $modalAction.on('click', function(e) {
            if ($actionButton.is("button")) {
                e.preventDefault();
                $actionButton.closest('form').submit();
            }
            else {
                $modal.modal('hide');
            }
        });
        $modal.on('hidden.bs.modal', function(e) {
            $modalTitle.text(titleText);
            $modalBody.text(bodyText);
            $modalAction.find('span').text(actionText);
        });
    });
})( jQuery );
