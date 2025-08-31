(function() {
    // Make sure bnDateConverter object exists
    if (typeof window.bnDateConverter === 'undefined') {
        console.error('Bangla Date Converter: Configuration not found');
        return;
    }

    function convertToBengaliDigits(text) {
        if (!text || typeof text !== 'string') return text;
        const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return text.replace(/[0-9]/g, function(digit) {
            return window.bnDateConverter.digits[englishDigits.indexOf(digit)];
        });
    }

    // Make processCustomSelectors available globally
    window.bnDateConverter.processCustomSelectors = function processCustomSelectors() {
        if (!window.bnDateConverter.selectors) return;
        
        const selectors = window.bnDateConverter.selectors
            .split('\n')
            .map(s => s.trim())
            .filter(s => s.length > 0);

        if (selectors.length === 0) return;

        try {
            selectors.forEach(selector => {
                try {
                    const elements = document.querySelectorAll(selector);
                    
                    elements.forEach(element => {
                        // Process all text nodes within the element
                        const walker = document.createTreeWalker(
                            element,
                            NodeFilter.SHOW_TEXT,
                            null,
                            false
                        );

                        let node;
                        while (node = walker.nextNode()) {
                            const originalText = node.nodeValue;
                            const convertedText = convertToBengaliDigits(originalText);
                            if (originalText !== convertedText) {
                                node.nodeValue = convertedText;
                            }
                        }
                    });
                } catch (selectorError) {
                    console.error('Invalid selector:', selector, selectorError);
                }
            });
        } catch (error) {
            console.error('Bangla Date Converter Error:', error);
        }
    }

    // Initial processing on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processCustomSelectors);
    } else {
        processCustomSelectors();
    }

    // Process dynamic content with debouncing
    let timeout;
    const observer = new MutationObserver(function() {
        clearTimeout(timeout);
        timeout = setTimeout(processCustomSelectors, 100);
    });

    // Start observing the document
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Initial call to process selectors
    window.bnDateConverter.processCustomSelectors();
})();
