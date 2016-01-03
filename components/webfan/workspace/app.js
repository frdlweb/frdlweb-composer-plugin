/*
@copyright (c) Till Wehowski - All rights reserved
@license (Basic/EULA) http://look-up.webfan.de/webdof-license
@license (Source Code Re-Usage) http://look-up.webfan.de/bsd-license
Copyright (c) 2015, Till Wehowski All rights reserved.
@component https://github.com/frdl/-Flow/tree/master/components/webfan/workspace
*/
(function(global){
    'use strict';
 
       frdl.each(document.querySelectorAll('*[data-frdl-component*="webfan/workspace"]'),  function(i,el){

      	  if('true'===el.getAttribute('data-frdl-component-loaded-script'))return true;
          el.setAttribute('data-frdl-component-initiated', 'true');
          el.setAttribute('data-frdl-component-loaded-script', 'true');
           	

           
             	
           	
      	  el.style.display='inline';
      	  el.style.position="relative";
          var a = frdl.Dom.create('a');
          
           if(true===frdl.Dom.isFramed()){
              a.style.display="none";
          }
                   
          var title=el.getAttribute('data-title');
          if('string' !== typeof title || ''===title)title='!workspace';
          
          if('string'===typeof el.getAttribute('data-decoration') && '' !== el.getAttribute('data-decoration')){
		  	a.style.textDecoration=el.getAttribute('data-decoration')
		  }
          if('string'===typeof el.getAttribute('data-class') && '' !== el.getAttribute('data-class')){
		  	a.setAttribute('class',el.getAttribute('data-class'));
		  }          
		  
		  a.style.cursor="pointer";
		  
          frdl.Dom.addText(title, a);
          a.addEventListener("click", function () {
             frdl.wd(true);
           }, false, true);
          
          var opened = el.getAttribute('data-opened').toString().toLowerCase();
          if('true'===opened || '1'===opened || 'yes' === opened){
              if(!frdl.Dom.isFramed() && !frdl.Dom.isVisible('desktop')){
                   frdl.wd(true);
              }
          }
          
          frdl.Dom.add(a, el);

       
     });


}(typeof exports !== 'undefined' ? global : (typeof this !== 'undefined' ? this : window)));
