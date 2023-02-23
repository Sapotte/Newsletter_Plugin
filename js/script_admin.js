
function preview(file) {

                console.log(URL.createObjectURL(file));
                var previewImg = jQuery("#preview-img");
                var urlImg = URL.createObjectURL(file);
                previewImg.attr('src', urlImg ) ;
                var date = new Date;
                date = date.toLocaleDateString();

                var title_content = "Newsletter du "+date;
                
                jQuery('#preview-title').html(title_content);

                jQuery("#preview").css("display", "block");
                    
}

