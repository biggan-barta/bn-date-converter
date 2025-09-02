(function() {
    // Exit early if configuration is not available
    if (typeof window.bnDateConverter === 'undefined' || !window.bnDateConverter.selectors) {
        return;
    }

    var processed = false;
    var digits = window.bnDateConverter.digits;

    function convertToBengaliDigits(text) {
        if (!text || typeof text !== 'string') return text;
        
        return text.replace(/[0-9]/g, function(digit) {
            return digits[parseInt(digit)];
        });
    }

    // Make processCustomSelectors available globally with throttling
    window.bnDateConverter.processCustomSelectors = function processCustomSelectors() {
        if (processed) return; // Prevent multiple executions
        
        var selectors = window.bnDateConverter.selectors
            .split('\n')
            .map(function(s) { return s.trim(); })
            .filter(function(s) { return s.length > 0; });

        if (selectors.length === 0) return;

        try {
            for (var i = 0; i < selectors.length; i++) {
                try {
                    var elements = document.querySelectorAll(selectors[i]);
                    
                    for (var j = 0; j < elements.length; j++) {
                        var element = elements[j];
                        
                        // Process only direct text nodes for better performance
                        if (element.childNodes.length === 1 && element.childNodes[0].nodeType === 3) {
                            var originalText = element.textContent;
                            var convertedText = convertToBengaliDigits(originalText);
                            if (originalText !== convertedText) {
                                element.textContent = convertedText;
                            }
                        } else {
                            // For complex elements, process text nodes
                            var walker = document.createTreeWalker(
                                element,
                                NodeFilter.SHOW_TEXT,
                                null,
                                false
                            );

                            var node;
                            while (node = walker.nextNode()) {
                                var originalText = node.nodeValue;
                                var convertedText = convertToBengaliDigits(originalText);
                                if (originalText !== convertedText) {
                                    node.nodeValue = convertedText;
                                }
                            }
                        }
                    }
                } catch (selectorError) {
                    // Silently skip invalid selectors
                }
            }
            processed = true;
        } catch (error) {
            // Silently handle errors
        }
    };

    // Process on DOM ready or immediately if already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.bnDateConverter.processCustomSelectors);
    } else {
        // Use setTimeout to avoid blocking
        setTimeout(window.bnDateConverter.processCustomSelectors, 0);
    }
})();
