jQuery(function () {
    var path = 'index.php?option=com_patchtester&tmpl=component&format=json';

    function initialize() {
        offset = 0;
        progress = 0;
        path = path + '&' + jQuery('#patchtester-token').attr('name') + '=1';
        getRequest('startfetch');
    };

    function getRequest(task) {
        jQuery.ajax({
            type: "GET",
            url: path,
            data: 'task=' + task,
            dataType: 'json',
            success: handleResponse,
            error: handleFailure
        });
    };

    function handleResponse(json, resp) {
        try {
            if (json === null) {
                throw resp;
            }
            if (json.error) {
                throw json;
            }

            jQuery('#patchtester-progress-message').html(json.message);

            if (json.data.header) {
                jQuery('#patchtester-progress-header').html(json.data.header);
            }

            if (json.data.complete) {
                // Nothing to do
            } else {
                // Send another request
                getRequest('fetch');
            }
        } catch (error) {
            try {
                if (json.error) {
                    jQuery('#patchtester-progress-header').text(Joomla.JText._('COM_PATCHTESTER_FETCH_AN_ERROR_HAS_OCCURRED'));
                    jQuery('#patchtester-progress-message').html(json.message);
                }
            } catch (ignore) {
                if (error === '') {
                    error = Joomla.JText._('COM_PATCHTESTER_NO_ERROR_RETURNED');
                }
                jQuery('#patchtester-progress-header').text(Joomla.JText._('COM_PATCHTESTER_FETCH_AN_ERROR_HAS_OCCURRED'));
                jQuery('#patchtester-progress-message').html(error);
            }
        }
        return true;
    };

    function handleFailure(xhr) {
        json = (typeof xhr == 'object' && xhr.responseText) ? xhr.responseText : null;
        jQuery('#patchtester-progress-header').text(Joomla.JText._('COM_PATCHTESTER_FETCH_AN_ERROR_HAS_OCCURRED'));
        jQuery('#patchtester-progress-message').html(json);
    };

    initialize();
});
