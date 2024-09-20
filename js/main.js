document.addEventListener("DOMContentLoaded", function ()
{
    //.img-thumbnail is class
    var allImages = document.querySelectorAll(".img-thumbnail");

    allImages.forEach (function (img) {
        img.originalWidth = img.style.width;
        img.originalHeight = img.style.height;

        img.addEventListener("click", function() {
            enlarge(img)
        });
    });



    function enlarge(img) {
        img.style.width = "20%";
        img.style.height = "35%";
        img.style.transition = "width 0.5s ease";
        img.style.position = "fixed";
        img.style.top = "50%";
        img.style.left = "50%";
        img.style.transform = "translate(-50%, -50%)";
        img.style.zIndex = "1000";

        var resetOnClickOutside = function (event) {
            if (!img.contains(event.target)) {
                reset(img, resetOnClickOutside);
            }
        };

        // Add a click event listener to reset the image size when clicked outside
        document.addEventListener("click", resetOnClickOutside);
    }

    function reset(img, resetOnClickOutside) {
        img.style.width = img.originalWidth;
        img.style.height = img.originalHeight;
        img.style.transition = "width 0.5s ease";
        img.style.position = "static";
        img.style.top = "auto";
        img.style.left = "auto";
        img.style.transform = "none";
        img.style.zIndex = "auto";

        document.removeEventListener("click", resetOnClickOutside);
    }

});