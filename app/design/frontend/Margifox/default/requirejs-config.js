var config = {
  map: {
    "*": {
        'slick': 'js/slick.min'
    }
    },
    paths:  {
       'slick': 'js/slick.min'
    },
    "shim": {
        'js/slick.min': { 
            deps: ['jquery']
        }
    },
    config: {
         mixins: {
             'Magento_Checkout/js/view/summary/abstract-total': {
                 'Magento_Checkout/js/view/summary/abstract-total.mixin': true
             },
             'Magento_Checkout/js/view/shipping-information': {
                 'Magento_Checkout/js/view/shipping-information.mixin': true
             },
             'Magento_Checkout/js/model/step-navigator': {
                 'Magento_Checkout/js/model/step-navigator.mixin': true
             }
         }
    }
}; 