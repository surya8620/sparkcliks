(function ($) {
  "use strict";

  // ============== Header Hide Click On Body Js Start ========
  $('.header-button').on('click', function () {
    $('.body-overlay').toggleClass('show')
  });
  $('.body-overlay').on('click', function () {
    $('.header-button').trigger('click')
    $(this).removeClass('show');
  });
  // =============== Header Hide Click On Body Js End =========

  // ==========================================
  //      Start Document Ready function
  // ==========================================
  $(document).ready(function () {

    // ========================== Header Hide Scroll Bar Js Start =====================
    $('.navbar-toggler.header-button').on('click', function () {
      $('body').toggleClass('scroll-hide-sm')
    });
    $('.body-overlay').on('click', function () {
      $('body').removeClass('scroll-hide-sm')
    });
    // ========================== Header Hide Scroll Bar Js End =====================

    /*==================== custom dropdown select js ====================*/
    $('.custom--dropdown > .custom--dropdown__selected').on('click', function () {
      $(this).parent().toggleClass('open');
    });
    $('.custom--dropdown > .dropdown-list > .dropdown-list__item').on('click', function () {
      $('.custom--dropdown > .dropdown-list > .dropdown-list__item').removeClass('selected');
      $(this).addClass('selected').parent().parent().removeClass('open').children('.custom--dropdown__selected').html($(this).html());
    });
    $(document).on('keyup', function (evt) {
      if ((evt.keyCode || evt.which) === 27) {
        $('.custom--dropdown').removeClass('open');
      }
    });
    $(document).on('click', function (evt) {
      if ($(evt.target).closest(".custom--dropdown > .custom--dropdown__selected").length === 0) {
        $('.custom--dropdown').removeClass('open');
      }
    });

    /*=============== custom dropdown select js end =================*/

    // ================== Password Show Hide Js Start ==========
    $(".toggle-password").on('click', function () {
      $(this).toggleClass(" fa-eye-slash");
      var input = $($(this).attr("id"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
    // =============== Password Show Hide Js End =================

    // ================== Sidebar Menu Js Start ===============
    // Sidebar Dropdown Menu Start
    $(".has-dropdown > a").click(function () {
      $(".sidebar-submenu").slideUp(200);
      if (
        $(this)
        .parent()
        .hasClass("active")
      ) {
        $(".has-dropdown").removeClass("active");
        $(this)
          .parent()
          .removeClass("active");
      } else {
        $(".has-dropdown").removeClass("active");
        $(this)
          .next(".sidebar-submenu")
          .slideDown(200);
        $(this)
          .parent()
          .addClass("active");
      }
    });
    // Sidebar Dropdown Menu End

    // Sidebar Icon & Overlay js 
    $(".dashboard-body__bar-icon").on("click", function () {
      $(".sidebar-menu").addClass('show-sidebar');
      $(".sidebar-overlay").addClass('show');
    });
    $(".sidebar-menu__close, .sidebar-overlay").on("click", function () {
      $(".sidebar-menu").removeClass('show-sidebar');
      $(".sidebar-overlay").removeClass('show');
    });
    // Sidebar Icon & Overlay js 
    // ===================== Sidebar Menu Js End =================

    // ==================== Dashboard User Profile Dropdown Start ==================
    $('.user-info__button').on('click', function () {
      $('.user-info-dropdown').toggleClass('show');
    });
    $('.user-info__button').attr('tabindex', -1).focus();

    $('.user-info__button').on('focusout', function () {
      $('.user-info-dropdown').removeClass('show');
    });
    // ==================== Dashboard User Profile Dropdown End ==================


  });
  // ==========================================
  //      End Document Ready function
  // ==========================================

  // ========================= Preloader Js Start =====================
  $(window).on("load", function () {
    $('.preloader').fadeOut();
  })
  // ========================= Preloader Js End=====================

  // ========================= Header Sticky Js Start ==============
  $(window).on('scroll', function () {
    if ($(window).scrollTop() >= 300) {
      $('.header').addClass('fixed-header');
    } else {
      $('.header').removeClass('fixed-header');
    }
  });
  // ========================= Header Sticky Js End===================

  //============================ Scroll To Top Icon Js Start =========
  var btn = $('.scroll-top');

  $(window).scroll(function () {
    if ($(window).scrollTop() > 300) {
      btn.addClass('show');
    } else {
      btn.removeClass('show');
    }
  });

  btn.on('click', function (e) {
    e.preventDefault();
    $('html, body').animate({
      scrollTop: 0
    }, '300');
  });
  //========================= Scroll To Top Icon Js End ======================


  let elements = document.querySelectorAll('[s-break]');
  Array.from(elements).forEach(element => {

    let html = element.innerHTML;

    if (typeof html != 'string') {
      return false;
    }
    html = html.split(" ");
    let breakLength = parseInt(element.getAttribute('s-break'));

    var colorText = [];
    if (breakLength < 0) {
      colorText = html.slice(breakLength);
    } else {
      colorText = html.slice(0, breakLength);
    }
    let solidText = [];
    html.filter(ele => {
      if (!colorText.includes(ele)) {
        solidText.push(ele);
      }
    });

    var color = element.getAttribute('s-color') || "bg--base px-1";
    colorText = `<span class="${color}">${colorText.toString().replaceAll(',', ' ')}</span>`;
    solidText = solidText.toString().replaceAll(',', ' ');
    breakLength < 0 ? element.innerHTML = `${solidText} ${colorText}` : element.innerHTML = `${colorText} ${solidText}`
  });

})(jQuery);
