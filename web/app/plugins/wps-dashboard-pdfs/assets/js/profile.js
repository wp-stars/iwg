jQuery(document).ready(function($) {
    $('#customerNotificationAboutNewDocuments').on('click', function(e) {
        e.preventDefault();
        $('#customerNotificationAboutNewDocuments').attr("disabled","disabled");

        $.ajax({
            url: profileNotifactionAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'profile_notification_profile_action',
                nonce: profileNotifactionAjax.nonce,
                user_id: profileNotifactionAjax.user_id,
            },
            success: function(response) {
                $('#notificationResponse').html(response);
            },
            error: function(result) {
                $('#notificationResponse').html('<span style="color:red;">Ein Fehler ist aufgetreten</span>');
                $('#customerNotificationAboutNewDocuments').removeAttr("disabled");
            }
        });
    });
});