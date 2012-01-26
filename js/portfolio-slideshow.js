/*!
 * jQuery imagesLoaded plugin v1.0.3
 * http://github.com/desandro/imagesloaded
 *
 * MIT License. by Paul Irish et al.
 */

(function($, undefined) {

  // $('#my-container').imagesLoaded(myFunction)
  // or
  // $('img').imagesLoaded(myFunction)

  // execute a callback when all images have loaded.
  // needed because .load() doesn't work on cached images

  // callback function gets image collection as argument
  //  `this` is the container

  $.fn.imagesLoaded = function( callback ) {
    var $this = this,
        $images = $this.find('img').add( $this.filter('img') ),
        len = $images.length,
        blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

    function triggerCallback() {
      callback.call( $this, $images );
    }

    function imgLoaded() {
      if ( --len <= 0 && this.src !== blank ){
        setTimeout( triggerCallback );
        $images.unbind( 'load error', imgLoaded );
      }
    }

    if ( !len ) {
      triggerCallback();
    }

    $images.bind( 'load error',  imgLoaded ).each( function() {
      // cached images don't fire load sometimes, so we reset src.
      if (this.complete || this.complete === undefined){
        var src = this.src;
        // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
        // data uri bypasses webkit log warning (thx doug jones)
        this.src = blank;
        this.src = src;
      }
    });

    return $this;
  };
})(jQuery);

/*!
 * Portfolio Slideshow Pro
 */
 
(function ($) {
	$(window).load(function() {
	
		var psLoader, psHash, psFluid;
		currSlide = new Array();
		psLoader = portfolioSlideshowOptions.psLoader;
		psFluid = portfolioSlideshowOptions.psFluid;
		psHash = portfolioSlideshowOptions.psHash;
	
		if ( jQuery.browser.msie && parseInt( jQuery.browser.version ) < 8 ) { //sets ie var to true if IE is 7 or below. Necessary for some style & functionality issues.
			ie = true; 
		} else { 
			ie = false 
		}
			
		
		if (psLoader === true) { //if we're supposed to show a loader
			$('.slideshow-wrapper').delay(1000).queue(function() {
				$('.portfolio-slideshow, .slideshow-nav, .pager').css('visibility', 'visible');
				$(this).removeClass("showloader");
			});	
		}
			
		$("div[id^=portfolio-slideshow]").each(function () {
				
			var num = this.id.match(/portfolio-slideshow(\d+)/)[1];
			
			/* Cache our jQuery objects*/
			
			/* Main slideshow elements */
			var slideshowwrapper = $('#slideshow-wrapper' + num);
			var slideshownav = $('#slideshow-nav' + num);
			var slideshow = $('#portfolio-slideshow' + num);
			var slideimage = slideshow.find('.slideshow-content img');
			
			/*Toggles*/
			var toggleshow = slideshowwrapper.find('.show');
			var togglehide = slideshowwrapper.find('.hide')
			var carousel = slideshowwrapper.find('.pscarousel'); 
			
			/*Nav elements*/
			var playbutton = slideshownav.find('.play');
			var pausebutton = slideshownav.find('.pause');
			var restartbutton = slideshownav.find('.restart');
				
			if ( ie === true ) {
				slideshowwrapper.addClass('ie');
			}

				$(function () {
					var index = 0, hash = window.location.hash;
					if (/\d+/.exec(hash)) {
					index = /\d+/.exec(hash)[0];
					index = (parseInt(index) || 1) - 1; // slides are zero-based
				}
	
				$.fn.cycle.updateActivePagerLink = function(pager, currSlideIndex) { 
				$(pager).find('img').removeClass('activeSlide') 
				.filter('#pager' + num + ' img:eq('+currSlideIndex+')').addClass('activeSlide'); 
				};
				
				
				function cyclePager() {
					slideshow.cycle({
						fx: psTrans[num],
						speed: psSpeed[num],
						timeout: psTimeout[num],
						nowrap: psNoWrap[num],
						next: '#slideshow-wrapper' + num + ' a.slideshow-next, #slideshow-wrapper' + num + ' #psnext' + num,
						startingSlide: index,
						prev: '#slideshow-wrapper' + num + ' a.slideshow-prev , #slideshow-wrapper' + num + ' #psprev' + num,
						before:	onBefore,
						after:	onAfter,
						end:	onEnd,
						slideExpr:	'.slideshow-content',
						manualTrump: true,
						slideResize: false,
						containerResize: false,
						pager:  '#pager' + num,
						cleartypeNoBg: true,
						pagerAnchorBuilder: buildAnchors
					});
				}	
				
				
			
				cyclePager();	
				
				slideimage.each(function() { //this gives each of the images a src attribute once the window has loaded
					$(this).attr('src',$(this).attr('data-img'));
				});
	
				//pause the slideshow right away if autoplay is off
				if ( psAutoplay[num] === false ) {
					slideshow.cycle('pause');
				} else {
			
					playbutton.fadeOut(100, function(){
					pausebutton.fadeIn(10);});	
				}
	
				
				//pause
				pausebutton.click(function() { 
					$(this).fadeOut(100, function(){
					playbutton.fadeIn(10);});
					slideshow.cycle('pause');
				});
			
				//play
				playbutton.click(function() { 
					slideshow.cycle('resume');
					$(this).fadeOut(100, function(){
					pausebutton.fadeIn(10);});
				});
	
				//restart
				restartbutton.click(function() { 
					$('#pager' + num + ' .numbers').empty();
					$(this).fadeOut(100, function(){
						pausebutton.fadeIn(10);});
						
						cyclePager();							
				
				});	
	
				//if ( psFluid === true ) {				
					$(window).resize(function() { //on window resize, force resize of the slideshows
						slideshow.css('width','').css('height','');	
						var $h, $w;
						$h = slideshow.find('.slideshow-content').eq(currSlide[num]).outerHeight();
						$w = slideshow.find('.slideshow-content').eq(currSlide[num]).width();
						slideshow.css("height", $h).css("width", $w);
					});
			
				//}	
				
				//build anchors
				function buildAnchors(idx, slide) { 
					return '#pager' +num+ ' img:eq(' + (idx) + ')'; 
				}
				
				function onBefore(curr,next,opts) {
					var slide = $(this);
					
					//this adjusts the height & width of the slideshow
					var $h,$w;
					$h = slide.height(); //slideshow content height
					$w = slide.width(); //slideshow content height
					slideshow.height($h).width($w);
						
					slide.find('img').imagesLoaded(function () {
						var $h,$w;
						$h = slide.parents('.slideshow-content').height(); //slideshow content height
						$w = slide.parents('.slideshow-content').width(); //slideshow content width
						slideshow.height($h).width($w);
	
					});
	
				}
					
				function onAfter(curr,next,opts) {
					var slide = $(this);
					currSlide[num] = opts.currSlide;		
					var $h,$w;
					$h = slide.height(); //slideshow content height
					$w = slide.width(); //slideshow content height
														
					if ( ie === true ) {
						slideshow.height($h).width($w);
					}
	
									
					if ( psNoWrap[num] === true ) { //if wrapping is disabled, fade out the controls at the appropriate time
						if (opts.currSlide === 0 ) {
							slideshownav.find('.slideshow-prev, .sep').addClass('inactive');
						} else {
							slideshownav.find('.slideshow-prev, .sep').removeClass('inactive');
						}
							
						if (opts.currSlide === opts.slideCount-1) {
							slideshownav.find('.slideshow-next, .sep').addClass('inactive');
						} else {
							slideshownav.find('.slideshow-next').removeClass('inactive');
						}
					}
	
					if (psHash === true) { 
						window.location.hash = opts.currSlide + 1;
					}
	
					var caption = (opts.currSlide + 1) + ' of ' + opts.slideCount;
					$('.slideshow-info' + num).html(caption);
				} 
	
				function onEnd() {
					slideshownav.find('.slideshow-next, .sep').addClass('inactive');
					pausebutton.hide();
					playbutton.hide();
					restartbutton.show();	
				}
				
			});
		});
	}); 
})(jQuery);
