#DobsonDev WordPress Media Library Upload Tester

A simple plugin for testing/illustrating you can use the WordPress media library in your own plugin/theme. The plugin creates an admin page where you can save image URL from the media library into a table created by the plugin. Some key features that some people asked for such as image previews have been included in the plugin so you can see how they work as well. Specific code examples and notes are highlighted below.

To install simply add this folder to your plugins folder (most commonly located at '/wp-content/plugins/').

###Image Preview

Image previewing is done through the JavaScript file. The line below along with the HTML `<div id="image-preview"></div>` is all you need to show a preview of the image.

```
$('#image-preview').html('<img src="' + $('#image-url').val() + '" />')
```

Other than this example the rest of the code is pretty self explanatory I think. A quick readthrough of the JavaScript will reveal almost everything else that's going on.

###Deleting Images

Although the plugin does have an option to delete images - these images are deleted only from the database table created by the plugin and are NOT deleted from the media library itself. This plugin doesn't really control the media library, only calls upon it to use.