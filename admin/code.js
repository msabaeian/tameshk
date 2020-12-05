/**
 * Created by Mohammad on 9/11/2016.
 */
$(document).ready(function(){
    $('.aside').slimScroll();

    $(".deleteCity").on('click',function() {
        var parent = $(this).parent();
        var cityId = parent.find("a").attr("cityId");
        $.post("actions/shopy.php",{action:"deleteCity",cityId:cityId},function(data) {
            if(data==="ok"){
                parent.fadeOut(1);
            }
        });
    });

    $('.addCityToSection').on('click',function() {
        var cityId = $(this).parent().find("option:selected").val();
        var citiesDiv = $(this).parent().parent().find("table");
        var sectionId = $(".sectionId").val();
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
        $.post("actions/shopy.php",{action:"addCity",cityId:cityId,sectionId:sectionId},function(data) {
            if(data.startsWith("ok")){
                citiesDiv.append('<tr> <td>'+data.replace("ok","")+'</td><td class="color-red pointer deleteCity">حذف</td></tr>');
            }else if(data == "repeat"){
                $(".addCityToSection").notify("شهر انتخاب شده از قبل وجود دارد!","error",{position:"left"});
            }
            $(".loader").remove();
        });
    });

    $('.addSection').on('click',function() {
        var sectionName = $(".sectionName").val();
        var table = $(this).parent().find("table");
        if(sectionName !== ''){
            $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
            $.post("actions/shopy.php",{action:"addSection",sectionName:sectionName},function(data) {
                console.log(data);
                if(data.startsWith("ok")){
                    console.log(data);
                    $(table).append(data.replace("ok",""));
                    $(".sectionName").val("");
                }else if(data == "repeat"){
                    $(".sectionName").notify("نام تکراری است!","error",{position:"left"});
                    $(".sectionName").val("");
                    $(".sectionName").focus();

                }
                $(".loader").remove();
            });
        }else{
            $(".sectionName").notify("نام بخش را وارد کنید","error",{position:"left"});
            $(".sectionName").focus();
            $(".loader").remove();
        }

    });

    $('.deleteSection').on('click',function() {
        var r = confirm("آیا مطمئن هستید؟ با اینکار تمامی محصولات، دسته بندی،شهر های فعال و تمامی داده های مربوط به این بخش حذف خواهند شد!");
        if (r == true) {
            var sectionId = $(this).attr("sectionId");
            var parent = $(this).parent().parent();
            $(this).parent().append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
            $.post("actions/shopy.php",{action:"deleteSection",sectionId:sectionId},function(data){
                parent.remove();
                $(".loader").remove();
            });
        }


    });

    $('.deleteShopImage').on('click',function() {
        var r = confirm("آیا مطمئن هستید؟!");
        if (r == true) {
            var shopimageid = $(this).attr("shopimageid");
            var parent = $(this).parent();
            $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
            $.post("actions/shopy.php",{action:"deleteShopImage",shopimageid:shopimageid},function(data){
                parent.remove();
                $(".loader").remove();
            });
        }


    });

    $('.addShopImage').on('click',function() {
        $(this).parent().parent().trigger("submit");
    });

    $('.deleteProductImage').on('click',function() {
        var r = confirm("آیا مطمئن هستید؟!");
        if (r == true) {
            var productImageId = $(this).attr("productimageid");
            var parent = $(this).parent();
            $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
            $.post("actions/shopy.php",{action:"deleteProductImage",productImageId:productImageId},function(data){
                parent.remove();
                $(".loader").remove();
            });
        }
    });

    $('.unActiveCategory').on('click',function() {
        var categoryId = $(this).attr("categoryId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"unActiveCategory",categoryId:categoryId},function(data){
            if(data == "ok"){
                cat.removeClass("color-red");
                cat.addClass("color-green");
                cat.removeClass("unActiveCategory");
                cat.addClass("activeCategory");
                cat.html("✔");
            }
            $(".loader").remove();
        });

    });
    
    $('.activeCategory').on('click',function() {
        var categoryId = $(this).attr("categoryId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"activeCategory",categoryId:categoryId},function(data){
            if(data == "ok"){
                cat.addClass("color-red");
                cat.removeClass("color-green");
                cat.addClass("unActiveCategory");
                cat.removeClass("activeCategory");
                cat.html("✖");
            }
            $(".loader").remove();
        });

    });
    $('.unActiveShopCategory').on('click',function() {
        var categoryId = $(this).attr("categoryId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"unActiveShopCategory",categoryId:categoryId},function(data){
            if(data == "ok"){
                cat.removeClass("color-red");
                cat.addClass("color-green");
                cat.removeClass("unActiveCategory");
                cat.addClass("activeCategory");
                cat.html("✔");
            }
            $(".loader").remove();
        });

    });

    $('.activeShopCategory').on('click',function() {
        var categoryId = $(this).attr("categoryId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"activeShopCategory",categoryId:categoryId},function(data){
            if(data == "ok"){
                cat.addClass("color-red");
                cat.removeClass("color-green");
                cat.addClass("unActiveCategory");
                cat.removeClass("activeCategory");
                cat.html("✖");
            }
            $(".loader").remove();
        });

    });

    $('.unActiveShop').on('click',function() {
        var shopId = $(this).attr("shopId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"unActiveShop",shopId:shopId},function(data){
            if(data == "ok"){
                cat.removeClass("color-red");
                cat.addClass("color-green");
                cat.removeClass("unActiveCategory");
                cat.addClass("activeCategory");
                cat.html("✔");
            }
            $(".loader").remove();
        });

    });

    $('.activeShop').on('click',function() {
        var shopId = $(this).attr("shopId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"activeShop",shopId:shopId},function(data){
            if(data == "ok"){
                cat.addClass("color-red");
                cat.removeClass("color-green");
                cat.addClass("unActiveCategory");
                cat.removeClass("activeCategory");
                cat.html("✖");
            }
            $(".loader").remove();
        });

    });

    $('.unActiveSection').on('click',function() {
        var sectionId = $(this).attr("sectionId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"unActiveSection",sectionId:sectionId},function(data){
            if(data == "ok"){
                cat.removeClass("color-red");
                cat.addClass("color-green");
                cat.removeClass("unActiveCategory");
                cat.addClass("activeCategory");
                cat.html("✔");
            }
            $(".loader").remove();
        });

    });

    $('.activeSection').on('click',function() {
        var sectionId = $(this).attr("sectionId");
        var cat = $(this);
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        $.post("actions/shopy.php",{action:"activeSection",sectionId:sectionId},function(data){
            if(data == "ok"){
                cat.addClass("color-red");
                cat.removeClass("color-green");
                cat.addClass("unActiveCategory");
                cat.removeClass("activeCategory");
                cat.html("✖");
            }
            $(".loader").remove();
        });

    });

    $('.rejectComment').on('click',function() {
        var commentId = $(this).attr("commentId");
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        var parent = $(this).parent().parent();
        $.post("actions/comment.php",{action:"reject",commentId:commentId},function(data){
            if(data == "ok"){
                parent.remove();
            }
            $(".loader").remove();
        });

    });
    $('.acceptComment').on('click',function() {
        var commentId = $(this).attr("commentId");
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        var parent = $(this).parent().parent();
        $.post("actions/comment.php",{action:"accept",commentId:commentId},function(data){
            if(data == "ok"){
                parent.remove();
            }
            $(".loader").remove();
        });

    });

    $('.rejectGallery').on('click',function() {
        var GalleryId = $(this).attr("GalleryId");
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        var parent = $(this).parent().parent();
        $.post("actions/gallery.php",{action:"reject",GalleryId:GalleryId},function(data){
            if(data == "ok"){
                parent.remove();
            }
            $(".loader").remove();
        });

    });
    $('.acceptGallery').on('click',function() {
        var GalleryId = $(this).attr("GalleryId");
        $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='15px'>");
        var parent = $(this).parent().parent();
        $.post("actions/gallery.php",{action:"accept",GalleryId:GalleryId},function(data){
            if(data == "ok"){
                parent.remove();
            }
            $(".loader").remove();
        });

    });

    $('.addCategory').on('click',function() {
        var categoryName = $(".categoryName").val();
        var sectionId = $(".sectionId").val();
        var table = $(this).parent().parent().find("table");
        if(categoryName !== ''){
            $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
            $.post("actions/shopy.php",{action:"addCategory",categoryName:categoryName,sectionId:sectionId},function(data) {
                console.log(data);
                if(data == "ok"){
                    $(table).append('<tr><td>'+categoryName+'</td></tr>');
                    $(".sectionName").val("");
                }else if(data == "repeat"){
                    $(".categoryName").notify("نام تکراری است!","error",{position:"left"});
                    $(".categoryName").val("");
                    $(".categoryName").focus();

                }
                $(".loader").remove();
            });
        }else{
            $(".categoryName").notify("نام دسته بندی را وارد کنید","error",{position:"left"});
            $(".categoryName").focus();
            $(".loader").remove();
        }

    });

    $('.addShopCategory').on('click',function() {
        var categoryName = $(".categoryNameShop").val();
        var sectionId = $(".sectionId").val();
        var table = $(this).parent().parent().find("table");
        if(categoryName !== ''){
            $(this).append("<img src='http://localhost/_dezFood/images/pre_loader.gif' class='loader' width='26px'>");
            $.post("actions/shopy.php",{action:"addShopCategory",categoryName:categoryName,sectionId:sectionId},function(data) {
                console.log(data);
                if(data == "ok"){
                    $(table).append('<tr><td>'+categoryName+'</td></tr>');
                    $(".sectionName").val("");
                }else if(data == "repeat"){
                    $(".categoryNameShop").notify("نام تکراری است!","error",{position:"left"});
                    $(".categoryNameShop").val("");
                    $(".categoryNameShop").focus();

                }
                $(".loader").remove();
            });
        }else{
            $(".categoryNameShop").notify("نام دسته بندی را وارد کنید","error",{position:"left"});
            $(".categoryNameShop").focus();
            $(".loader").remove();
        }

    });
});