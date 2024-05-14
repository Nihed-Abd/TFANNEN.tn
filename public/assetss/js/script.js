/*-----------------------------------------------------------------

Template Name:  Odor - Vape Store WooCommerce HTML Template
Author:  Gramentheme
Author URI: https://themeforest.net/user/gramentheme/portfolio
Developer: Kawser Ahmed Roni
Version: 1.0.0
Description: Odor - Vape Store WooCommerce HTML Templatee

-------------------------------------------------------------------
CSS TABLE OF CONTENTS
-------------------------------------------------------------------

01. preloader
02. header
03. swiper slider
04. animated text with swiper slider
05. shop products count
06. image src change
07. hide & show a div
08. isotope
09. add class & remove class
10. magnificPopup
11. back to top
12. data backgrund
13. coundown by click
14. remove products
15. wow animation
15. Custom cursor

------------------------------------------------------------------*/

(function ($) {
	("use strict");

	$(".header-bar").on("click", function (e) {
		$(".main-menu, .header-bar").toggleClass("active");
	});
	$(".main-menu li a").on("click", function (e) {
		var element = $(this).parent("li");
		if (element.hasClass("open")) {
			element.removeClass("open");
			element.find("li").removeClass("open");
			element.find("ul").slideUp(300, "swing");
		} else {
			element.addClass("open");
			element.children("ul").slideDown(300, "swing");
			element.siblings("li").children("ul").slideUp(300, "swing");
			element.siblings("li").removeClass("open");
			element.siblings("li").find("li").removeClass("open");
			element.siblings("li").find("ul").slideUp(300, "swing");
		}
	});
	var fixed_top = $(".header-section");
	$(window).on("scroll", function () {
		if ($(this).scrollTop() > 220) {
			fixed_top.addClass("menu-fixed animated fadeInDown");
			fixed_top.removeClass("slideInUp");
			$("body").addClass("body-padding");
		} else {
			fixed_top.removeClass("menu-fixed fadeInDown");
			fixed_top.addClass("slideInUp");
			$("body").removeClass("body-padding");
		}
	});

	// Preloader area start here ***
	paceOptions = {
		ajax: true,
		document: true,
		eventLag: false,
	};

	Pace.on("done", function () {
		$("#preloader").addClass("isdone");
		$(".loading").addClass("isdone");
	});
	// Preloader area end here ***

	// Banner five slider area end here ***
	var sliderActive3 = ".banner-two__slider";
	var sliderInit3 = new Swiper(sliderActive3, {
		loop: true,
		slidesPerView: 1,
		effect: "fade",
		speed: 3000,
		autoplay: {
			delay: 7000,
			disableOnInteraction: false,
		},
		navigation: {
			nextEl: ".banner-two__arry-next",
			prevEl: ".banner-two__arry-prev",
		},
	});
	// Here this is use for animate ***
	function animated_swiper(selector, init) {
		var animated = function animated() {
			$(selector + " [data-animation]").each(function () {
				var anim = $(this).data("animation");
				var delay = $(this).data("delay");
				var duration = $(this).data("duration");
				$(this)
					.removeClass("anim" + anim)
					.addClass(anim + " animated")
					.css({
						webkitAnimationDelay: delay,
						animationDelay: delay,
						webkitAnimationDuration: duration,
						animationDuration: duration,
					})
					.one("animationend", function () {
						$(this).removeClass(anim + " animated");
					});
			});
		};
		animated();
		init.on("slideChange", function () {
			$(sliderActive3 + " [data-animation]").removeClass("animated");
		});
		init.on("slideChange", animated);
	}
	animated_swiper(sliderActive3, sliderInit3);
	// Banner five slider area end here ***

	// Product slider area start here ***
	var swiper = new Swiper(".product__slider", {
		spaceBetween: 24,
		speed: 300,
		loop: true,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
		pagination: {
			el: ".product__dot",
			clickable: true,
		},
		breakpoints: {
			575: {
				slidesPerView: 2,
			},
		},
	});
	// Product slider area end here ***

	// Gallery slider area start here ***
	var swiper = new Swiper(".gallery__slider", {
		spaceBetween: 30,
		speed: 300,
		loop: true,
		centeredSlides: true,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
		breakpoints: {
			1300: {
				slidesPerView: 4,
			},
			991: {
				slidesPerView: 3,
			},
			768: {
				slidesPerView: 2,
			},
		},
	});
	// Gallery slider area end here ***

	// Category slider area start here ***
	var swiper = new Swiper(".category__slider", {
		spaceBetween: 30,
		speed: 500,
		loop: true,
		autoplay: {
			delay: 3000,
			disableOnInteraction: false,
		},
		breakpoints: {
			1440: {
				slidesPerView: 6,
			},
			1300: {
				slidesPerView: 5,
			},
			991: {
				slidesPerView: 4,
			},
			768: {
				slidesPerView: 3,
			},
			500: {
				slidesPerView: 2,
			},
		},
	});
	// Category slider area end here ***

	// Brand slider area start here ***
	var swiper = new Swiper(".brand__slider", {
		spaceBetween: 30,
		speed: 300,
		loop: true,
		autoplay: {
			delay: 3000,
			disableOnInteraction: false,
		},
		breakpoints: {
			1440: {
				slidesPerView: 6,
			},
			1300: {
				slidesPerView: 5,
			},
			991: {
				slidesPerView: 4,
			},
			768: {
				slidesPerView: 3,
			},
			500: {
				slidesPerView: 2,
			},
		},
	});
	// Brand slider area end here ***

	// Testimonial two slider area start here ***
	var swiper = new Swiper(".testimonial__slider", {
		loop: "true",
		spaceBetween: 20,
		speed: 500,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		// pagination: {
		// 	el: ".testimonial-two__dot",
		// 	clickable: true,
		// },
		navigation: {
			nextEl: ".testimonial__arry-next",
			prevEl: ".testimonial__arry-prev",
		},
	});
	// Testimonial two slider area end here ***

	// Blog slider area start here ***
	var swiper = new Swiper(".blog__slider", {
		loop: "true",
		spaceBetween: 30,
		speed: 500,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
		pagination: {
			el: ".blog__dot",
			clickable: true,
		},
	});
	// Blog slider area end here ***

	// Get swiper slider area start here ***
	var swiper = new Swiper(".get__slider", {
		loop: "true",
		spaceBetween: 10,
		speed: 300,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		navigation: {
			nextEl: ".get-now__arry-right",
			prevEl: ".get-now__arry-left",
		},
	});
	// Get swiper slider area end here ***

	// Isotope area start here ***
	var $grid = $(".filter__items").isotope({});
	// click here
	$(".filter__list").on("click", "li", function () {
		var filterValue = $(this).attr("data-filter");
		$grid.isotope({ filter: filterValue });
	});
	// change is-checked class on buttons
	$(".filter__list").each(function (i, buttonGroup) {
		var $buttonGroup = $(buttonGroup);
		$buttonGroup.on("click", "li", function () {
			$buttonGroup.find(".active").removeClass("active");
			$(this).addClass("active");
		});
	});
	// Isotope area end here ***

	// Background image date area start here ***
	$("[data-background").each(function () {
		$(this).css(
			"background-image",
			"url( " + $(this).attr("data-background") + "  )"
		);
	});
	// Background image date area end here ***

	// Video popup area start here ***
	$(".video-popup").magnificPopup({
		type: "iframe",
		iframe: {
			markup:
				'<div class="mfp-iframe-scaler">' +
				'<div class="mfp-close"></div>' +
				'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
				"</div>",

			patterns: {
				youtube: {
					index: "youtube.com/",

					id: "v=",

					src: "https://www.youtube.com/embed/%id%?autoplay=1",
				},
				vimeo: {
					index: "vimeo.com/",
					id: "/",
					src: "//player.vimeo.com/video/%id%?autoplay=1",
				},
				gmaps: {
					index: "//maps.google.",
					src: "%id%&output=embed",
				},
			},

			srcAction: "iframe_src",
		},
	});
	// Video popup area end here ***

	// Map popup area start here ***
	$(".map-popup").magnificPopup({
		disableOn: 700,
		type: "iframe",
		mainClass: "mfp-fade",
		removalDelay: 160,
		preloader: false,
		fixedContentPos: false,
	});
	// Map popup area end here ***

	// Counter up area start here ***
	$(".count").counterUp({
		delay: 20,
		time: 3000,
	});
	// Counter up area end here ***

	// Countdown area start here ***
	var targetDate = new Date("2023-12-01 00:00:00").getTime();
	// fix you date here
	var countdownInterval = setInterval(function () {
		var currentDate = new Date().getTime();
		var remainingTime = targetDate - currentDate;

		var days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
		var hours = Math.floor(
			(remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
		);
		var minutes = Math.floor(
			(remainingTime % (1000 * 60 * 60)) / (1000 * 60)
		);
		var seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

		// Pad single-digit values with leading zeros
		$("#day").text(days.toString().padStart(2, "0"));
		$("#hour").text(hours.toString().padStart(2, "0"));
		$("#min").text(minutes.toString().padStart(2, "0"));
		$("#sec").text(seconds.toString().padStart(2, "0"));

		if (remainingTime <= 0) {
			clearInterval(countdownInterval);
			// You can add a message or perform any action when the countdown timer reaches zero
		}
	}, 1000);
	// Countdown area end here ***

	// Shop single swiper slider area start here ***
	var swiper = new Swiper(".shop-slider-thumb", {
		loop: true,
		spaceBetween: 10,
		slidesPerView: 4,
		freeMode: true,
		watchSlidesProgress: true,
		navigation: {
			nextEl: ".right-arry",
			prevEl: ".left-arry",
		},
	});
	var swiper2 = new Swiper(".shop-single-slide", {
		loop: "true",
		spaceBetween: 20,
		speed: 300,
		grabCursor: true,
		navigation: {
			nextEl: ".right-arry",
			prevEl: ".left-arry",
		},
		thumbs: {
			swiper: swiper,
		},
	});
	// Shop single swiper slider area end here ***

	// Shop count js area start here ***
	$(".quantity").on("click", ".plus", function (e) {
		let $input = $(this).prev("input.qty");
		let val = parseInt($input.val(), 10); // Specify base 10
		$input.val(val + 1).change();
	});
	$(".quantity").on("click", ".minus", function (e) {
		let $input = $(this).next("input.qty");
		var val = parseInt($input.val(), 10); // Specify base 10
		if (val > 0) {
			$input.val(val - 1).change();
		}
	});
	// Shop count js area end here ***

	// Shop image zoom js area start here ***
	// $(document).ready(function () {
	// 	$(".product-image-container")
	// 		.on("mousemove", function (e) {
	// 			var parentOffset = $(this).offset();
	// 			var relX = e.pageX - parentOffset.left;
	// 			var relY = e.pageY - parentOffset.top;
	// 			var width = $(this).width();
	// 			var height = $(this).height();
	// 			var zoomX = (relX / width) * 100;
	// 			var zoomY = (relY / height) * 100;
	// 			$(this)
	// 				.find(".zoomed-image")
	// 				.css({
	// 					"background-image": "url(assets/images/shop/02.jpg)", // Path to your product image
	// 					"background-position": zoomX + "% " + zoomY + "%",
	// 					display: "block",
	// 				});
	// 		})
	// 		.mouseleave(function () {
	// 			$(this).find(".zoomed-image").hide();
	// 		});
	// });

	// Shop image zoom js area end here ***

	// Hide & show by clicks js area start here ***
	$(document).on("click", "#openButton", function () {
		$("#targetElement").removeClass("side_bar_hidden");
	});
	$(document).on("click", "#closeButton", function () {
		$("#targetElement").addClass("side_bar_hidden");
	});
	// Hide & show by clicks js area end here ***

	// Radio btn area start here ***
	$(document).on("click", ".radio-btn span", function () {
		$(this).toggleClass("radio-btn-active");
	});
	// Radio btn area end here ***

	// Mouse cursor area start here ***
	function mousecursor() {
		if ($("body")) {
			const e = document.querySelector(".cursor-inner"),
				t = document.querySelector(".cursor-outer");
			let n,
				i = 0,
				o = !1;
			(window.onmousemove = function (s) {
				o ||
					(t.style.transform =
						"translate(" + s.clientX + "px, " + s.clientY + "px)"),
					(e.style.transform =
						"translate(" + s.clientX + "px, " + s.clientY + "px)"),
					(n = s.clientY),
					(i = s.clientX);
			}),
				$("body").on("mouseenter", "a, .cursor-pointer", function () {
					e.classList.add("cursor-hover"), t.classList.add("cursor-hover");
				}),
				$("body").on("mouseleave", "a, .cursor-pointer", function () {
					($(this).is("a") && $(this).closest(".cursor-pointer").length) ||
						(e.classList.remove("cursor-hover"),
						t.classList.remove("cursor-hover"));
				}),
				(e.style.visibility = "visible"),
				(t.style.visibility = "visible");
		}
	}

	$(function () {
		mousecursor();
	});
	// Mouse cursor area end here ***

	// Nice seclect area start here ***
	$(document).ready(function () {
		$("select").niceSelect();
	});
	// Nice seclect area end here ***

	// Back to top area start here ***
	var scrollPath = document.querySelector(".scroll-up path");
	var pathLength = scrollPath.getTotalLength();
	scrollPath.style.transition = scrollPath.style.WebkitTransition = "none";
	scrollPath.style.strokeDasharray = pathLength + " " + pathLength;
	scrollPath.style.strokeDashoffset = pathLength;
	scrollPath.getBoundingClientRect();
	scrollPath.style.transition = scrollPath.style.WebkitTransition =
		"stroke-dashoffset 10ms linear";
	var updatescroll = function () {
		var scroll = $(window).scrollTop();
		var height = $(document).height() - $(window).height();
		var scroll = pathLength - (scroll * pathLength) / height;
		scrollPath.style.strokeDashoffset = scroll;
	};
	updatescroll();
	$(window).scroll(updatescroll);
	var offset = 50;
	var duration = 950;
	jQuery(window).on("scroll", function () {
		if (jQuery(this).scrollTop() > offset) {
			jQuery(".scroll-up").addClass("active-scroll");
		} else {
			jQuery(".scroll-up").removeClass("active-scroll");
		}
	});
	jQuery(".scroll-up").on("click", function (event) {
		event.preventDefault();
		jQuery("html, body").animate(
			{
				scrollTop: 0,
			},
			duration
		);
		return false;
	});
	// Back to top area end here ***

	// Change the root color area start here ***
	function setThemeColor(color) {
		const root = document.documentElement;
		root.setAttribute("data-theme", color);
	}
	// Change the root color area end here ***

	// WOW Animatin area start here ***
	new WOW().init();
	// WOW Animatin area start here ***
})(jQuery);
