$(function () {
    $('#shorten').on('click', function () {
        var resultLink = $('#result-link'),
            helpBlock = $('#errors-block'),
            origLink = $('#orig-link'),
            originalLinkValue = origLink.find('input').val();

        helpBlock.html('');
        $('.form-group').removeClass('has-error has-success');

        if (!originalLinkValue) {
            origLink.addClass('has-error');
            return;
        }

        $.ajax({
                method: 'POST',
                url: '/generate-link',
                data: {originalLink: originalLinkValue}
            })
            .done(function (response) {
                if (response.success) {
                    resultLink.closest('.form-group').addClass('has-success');
                    resultLink.val(response.result.url);
                } else {
                    resultLink.val('');
                    origLink.addClass('has-error');
                    helpBlock.append(response.errors.join("<br/> "));
                }
            });
    });
});