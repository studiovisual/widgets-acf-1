(function($) {
    if(typeof acf === 'undefined')
        return;
    
    window.modal = {
        modals: [],
        
        // Open
        open: function($target, args) {
            var model = this;
            
            args = acf.parseArgs(args, {
                title: '',
                footer: false,
                size: false,
                destroy: false,
                onOpen: false,
                onClose: false,
            });
            
            model.args = args;
            
            $target.addClass('-open');
            
            if(args.size)
                $target.addClass('-' + args.size);
            
            if(!$target.find('> .widgets-acf-modal-wrapper').length)
                $target.wrapInner('<div class="widgets-acf-modal-wrapper" />');
            
            if(!$target.find('> .widgets-acf-modal-wrapper > .widgets-acf-modal-content').length)
                $target.find('> .widgets-acf-modal-wrapper').wrapInner('<div class="widgets-acf-modal-content" />');
            
            $target.find('> .widgets-acf-modal-wrapper').prepend('<div class="widgets-acf-modal-wrapper-overlay"></div><div class="widgets-acf-modal-title"><span class="title">' + args.title + '</span><button class="close"></button></div>');
            
            $target.find('.widgets-acf-modal-title > .close').click(function(e) {
                e.preventDefault();
                model.close(args);
            });
            
            if(args.footer) {
                $target.find('> .widgets-acf-modal-wrapper').append('<div class="widgets-acf-modal-footer"><button class="button button-primary">' + args.footer + '</button></div>');
                
                $target.find('.widgets-acf-modal-footer > button').click(function(e){
                    e.preventDefault();
                    model.close(args);
                });
            }
            
            modal.modals.push($target);
            
            var $body = $('body');
            
            if(!$body.hasClass('widgets-acf-modal-opened')) {
				var overlay = $('<div class="widgets-acf-modal-overlay" />');
                
				$body.addClass('widgets-acf-modal-opened').append(overlay);
                
                $body.find('.widgets-acf-modal-overlay').click(function(e) {
                    e.preventDefault();
                    model.close(model.args);
                });
                
                $(window).keydown(function(e) {
                    if(e.keyCode !== 27 || !$('body').hasClass('widgets-acf-modal-opened'))
                        return;
                    
                    e.preventDefault();
                    model.close(model.args);
                });
			}
            
            modal.multiple();
            modal.onOpen($target, args);
            
            return $target;
		},
		
        // Close
		close: function(args) {
            args = acf.parseArgs(args, {
                destroy: false,
                onClose: false,
            });
            
            var $target = modal.modals.pop();
			
			$target.find('.widgets-acf-modal-wrapper-overlay').remove();
			$target.find('.widgets-acf-modal-title').remove();
			$target.find('.widgets-acf-modal-footer').remove();
            
			$target.removeAttr('style');
            
			//$target.removeClass('-open -small -medium -full');
			$target.removeClass('-open');
            
            if(args.destroy)
                $target.remove();
                
			if(!modal.modals.length) {
				$('.widgets-acf-modal-overlay').remove();
                $('body').removeClass('widgets-acf-modal-opened');
			}
            
            modal.multiple();
            modal.onClose($target, args);
		},
        
        // Multiple
        multiple: function() {
            var last = modal.modals.length - 1;
            
            $.each(modal.modals, function(i) {
                if(last == i) {
                    $(this).removeClass('widgets-acf-modal-sub').css('margin-left', '');
                    return;
                }
                
                $(this).addClass('widgets-acf-modal-sub').css('margin-left',  - (500 / (i+1)));
			});
        },
        
        onOpen: function($target, args) {
            if(!args.onOpen || !(args.onOpen instanceof Function))
                return;
            
            args.onOpen($target);
        },
        
        onClose: function($target, args) {
            if(!args.onClose || !(args.onClose instanceof Function))
                return;
            
            args.onClose($target);
        }
    };   
})(jQuery);