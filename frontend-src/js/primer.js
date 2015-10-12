// matches polyfill
this.Element && function(ElementPrototype) {
    ElementPrototype.matches = ElementPrototype.matches ||
    ElementPrototype.matchesSelector ||
    ElementPrototype.webkitMatchesSelector ||
    ElementPrototype.msMatchesSelector ||
    function(selector) {
        var node = this, nodes = (node.parentNode || node.document).querySelectorAll(selector), i = -1;
        while (nodes[++i] && nodes[i] != node);
        return !!nodes[i];
    }
}(Element.prototype);

// closest polyfill
this.Element && function(ElementPrototype) {
    ElementPrototype.closest = ElementPrototype.closest ||
    function(selector) {
        var el = this;
        while (el.matches && !el.matches(selector)) el = el.parentNode;
        return el.matches ? el : null;
    }
}(Element.prototype);

// Utility Functions
var $r = {
    hasClass: function(el, className) {
        return el.classList ? el.classList.contains(className) : new RegExp('\\b'+ className+'\\b').test(el.className);
    },

    addClass: function(el, className) {
        if (el.classList) el.classList.add(className);
        else if (!hasClass(el, className)) el.className += ' ' + className;
    },

    removeClass: function(el, className) {
        if (el.classList) el.classList.remove(className);
        else el.className = el.className.replace(new RegExp('\\b'+ className+'\\b', 'g'), '');
    },

    toggleClass: function(el, className) {
        if(hasClass(el, className)) {
            removeClass(el, className);
        }
        else {
            addClass(className);
        }
    }
}


// Cut the mustard
if ( 'querySelector' in document && 'addEventListener' in window ) {

    // Handle the tabs
    document.addEventListener('DOMContentLoaded', function() {
        var list = document.getElementsByClassName('primer-tabs');

        // iterate over elements and output their HTML content
        [].forEach.call(list, function(el) {
            // Create tab controls
            var tabButtons = document.createElement('div');
            $r.addClass(tabButtons, 'primer-tabs__controls');
            el.insertBefore(tabButtons, el.childNodes[0]);

            [].forEach.call(el.querySelectorAll('.primer-tab'), function(tab) {
                // Remove the title
                var title = tab.querySelector('.primer-tab__title');
                var content = tab.querySelector('.primer-tab__content');
                title.parentElement.removeChild(title);

                // Create a tab button
                title = title.innerText;

                var button = document.createElement('button');
                $r.addClass(button, 'primer-tabs__control');
                button.setAttribute('title', title);
                button.appendChild(document.createTextNode(title));

                tabButtons.appendChild(button);

                button.addEventListener('click', function(event) {
                    // Hide all the tabs for this control
                    [].forEach.call(el.querySelectorAll('.primer-tab__content'), function(content) {
                        content.style.display = 'none';
                    });

                    content.style.display = 'block';

                    // Add the correct class to the buttons
                    [].forEach.call(tabButtons.querySelectorAll('.primer-tabs__control'), function(control) {
                        $r.removeClass(control, 'primer-tab__control--current');
                    });

                    $r.addClass(event.target, 'primer-tab__control--current');
                });
            });

            // Select the default tab to show
            tabButtons.querySelector('.primer-tabs__control:first-child').click();
        });
    });

    // Handle show/hide JSON data
    document.addEventListener('DOMContentLoaded', function() {

        var list = document.getElementsByClassName('primer-pattern__data');

        // iterate over elements and output their HTML content
        [].forEach.call(list, function(el) {
            el.style.display = 'none';

            // Create a button element
            var button = document.createElement('button');
            $r.addClass(button, 'primer-pattern__toggle-button');
            $r.addClass(button, 'primer-pattern__toggle-button--data');
            button.setAttribute('title', 'Show Data');
            button.appendChild(document.createTextNode('Show Data'));

            // Get the place for the button to be positioned
            var actions = el.closest('.primer-pattern').querySelectorAll('.primer-pattern__actions')[0]
            actions.insertBefore(button, actions.childNodes[0]);

            // Listen for interactions on the button
            button.addEventListener('click', function(event) {
                event.preventDefault();

                if(el.offsetParent === null) {
                    // Hidden so show it
                    el.style.display = '';
                    $r.addClass(button, 'primer-pattern__toggle-button--active');
                    button.innerText = 'Hide Data';
                }
                else {
                    // Visible so hide it
                    el.style.display = 'none';
                    $r.removeClass(button, 'primer-pattern__toggle-button--active');
                    button.innerText = 'Show Data';
                }
            });
        });

    });

    // Handle show/hide HTML markup
    document.addEventListener('DOMContentLoaded', function() {

        var list = document.getElementsByClassName('primer-pattern__html');

        // iterate over elements and output their HTML content
        [].forEach.call(list, function(el) {
            el.style.display = 'none';

            // Create a button element
            var button = document.createElement('button');
            $r.addClass(button, 'primer-pattern__toggle-button');
            $r.addClass(button, 'primer-pattern__toggle-button--code');
            button.setAttribute('title', 'Show Markup');
            button.appendChild(document.createTextNode('Show Markup'));

            // Get the place for the button to be positioned
            var actions = el.closest('.primer-pattern').querySelectorAll('.primer-pattern__actions')[0]
            actions.insertBefore(button, actions.childNodes[0]);

            // Listen for interactions on the button
            button.addEventListener('click', function(event) {
                event.preventDefault();

                if(el.offsetParent === null) {
                    // Hidden so show it
                    el.style.display = '';
                    $r.addClass(button, 'primer-pattern__toggle-button--active');
                    button.innerText = 'Hide Markup';
                }
                else {
                    // Visible so hide it
                    el.style.display = 'none';
                    $r.removeClass(button, 'primer-pattern__toggle-button--active');
                    button.innerText = 'Show Markup';
                }
            });
        });

    });

    // Handle show/hide descriptions
    document.addEventListener('DOMContentLoaded', function() {

        var list = document.getElementsByClassName('primer-pattern__copy');

        // iterate over elements and output their HTML content
        [].forEach.call(list, function(el) {
            el.style.display = 'none';

            // Create a button element
            var button = document.createElement('button');
            $r.addClass(button, 'primer-pattern__toggle-button');
            $r.addClass(button, 'primer-pattern__toggle-button--readme');
            button.setAttribute('title', 'Show Notes');
            button.appendChild(document.createTextNode('Show Notes'));

            // Get the place for the button to be positioned
            var actions = el.closest('.primer-pattern').querySelectorAll('.primer-pattern__actions')[0]
            actions.insertBefore(button, actions.childNodes[0]);

            // Listen for interactions on the button
            button.addEventListener('click', function(event) {
                event.preventDefault();

                if(el.offsetParent === null) {
                    // Hidden so show it
                    el.style.display = '';
                    $r.addClass(button, 'primer-pattern__toggle-button--active');
                    button.innerText = 'Hide Notes';
                }
                else {
                    // Visible so hide it
                    el.style.display = 'none';
                    $r.removeClass(button, 'primer-pattern__toggle-button--active');
                    button.innerText = 'Show Notes';
                }
            });
        });

    });

    // Provide a minimal view toggle
    document.addEventListener('DOMContentLoaded', function(){

        var body = document.querySelector('body');

        if($r.hasClass(body, 'is-template')) {
            return;
        }

        var toggle = document.createElement('div');
        $r.addClass(toggle, 'primer-minimal-toggle');

        var isCurrentlyMinimal = window.location.href.match(/(\?|&)minimal/);

        if(isCurrentlyMinimal) {
            toggle.innerText = 'Show chrome';
        }
        else {
            toggle.innerText = 'Show minimal';
        }

        body.appendChild(toggle);

        toggle.addEventListener('click', function(event) {
            var url = window.location.href;

            if(isCurrentlyMinimal) {
                url = url.replace(/(&|\?)minimal/, '');
            }
            else {
                // Check if we already have a query string
                if(window.location.search.length) {
                    url += '&minimal';
                }
                else {
                    url += '?minimal';
                }
            }

            window.location = url;
        });

    });

    // Add Prism highlighting
    document.addEventListener('DOMContentLoaded', function() {
        if('Prism' in window) {

            var list = document.querySelectorAll('.primer-pattern__html pre code, .primer-pattern__data pre code');

            // iterate over elements and output their HTML content
            [].forEach.call(list, function(el) {

                Prism.highlightElement(el);
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var body = document.querySelector('body');

        // Only show the menu on the non-template pages
        if($r.hasClass(body, 'is-template')) {
            return;
        }

        var httpRequest;
        if (window.XMLHttpRequest) { // Mozilla, Safari, IE7+ ...
            httpRequest = new XMLHttpRequest();
        } else if (window.ActiveXObject) { // IE 6 and older
            httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
        }

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState === 4) {
                if (httpRequest.status === 200) {
                    var response = httpRequest.responseText;

                    var nav = document.createElement('div');
                    $r.addClass(nav, 'primer-menu');

                    nav.innerHTML = response + '<button class="primer-menu__button">Menu</button>';

                    body.appendChild(nav);

                    nav.addEventListener('click', function(event) {
                        if($r.hasClass(nav, 'visible')) {
                            $r.removeClass(nav, 'visible');
                        }
                        else {
                            $r.addClass(nav, 'visible');
                        }
                    });
                }
            }
        };

        httpRequest.open('GET', '/menu', true);
        httpRequest.send(null);
    });
}