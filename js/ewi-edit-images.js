jQuery(document).ready(function ($) {
    // Handle click on product gallery images
    $(document).on('click', '.woocommerce-product-gallery__image img', function (e) {
        e.preventDefault();

        const imageID = $(this).closest('li').find('input.attachment_id').val();

        if (imageID) {
            // Fetch image metadata via AJAX
            $.ajax({
                url: ewiData.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'ewi_get_image_metadata',
                    image_id: imageID,
                    nonce: ewiData.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        const data = response.data;

                        // Populate modal fields with current image metadata
                        $('#ewi-modal input[name="alt_text"]').val(data.alt_text);
                        $('#ewi-modal input[name="title"]').val(data.title);
                        $('#ewi-modal input[name="caption"]').val(data.caption);
                        $('#ewi-modal textarea[name="description"]').val(data.description);

                        // Show modal
                        $('#ewi-modal').fadeIn();

                        // Save button handler
                        $('#ewi-save-button').off('click').on('click', function () {
                            const altText = $('#ewi-modal input[name="alt_text"]').val();
                            const title = $('#ewi-modal input[name="title"]').val();
                            const caption = $('#ewi-modal input[name="caption"]').val();
                            const description = $('#ewi-modal textarea[name="description"]').val();

                            // Save metadata via AJAX
                            $.ajax({
                                url: ewiData.ajaxUrl,
                                method: 'POST',
                                data: {
                                    action: 'ewi_save_image_metadata',
                                    image_id: imageID,
                                    alt_text: altText,
                                    title: title,
                                    caption: caption,
                                    description: description,
                                    nonce: ewiData.nonce,
                                },
                                success: function (saveResponse) {
                                    if (saveResponse.success) {
                                        alert('Image metadata updated successfully.');
                                        $('#ewi-modal').fadeOut();
                                    } else {
                                        alert('Failed to save metadata.');
                                    }
                                },
                            });
                        });
                    } else {
                        alert('Failed to fetch image metadata.');
                    }
                },
            });
        }
    });

    // Close modal
    $('#ewi-modal-close').on('click', function () {
        $('#ewi-modal').fadeOut();
    });
});
