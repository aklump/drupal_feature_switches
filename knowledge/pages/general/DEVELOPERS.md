<!--
id: developers
tags: ''
-->

# Developers

## PHPUnit Testing

```php
  public static function setUpBeforeClass(): void {
  
    // Setup the operator her with your feature switches.
    FeatureSwitches::getOperator()
      ->add(Feature::create('mailchimp')
        ->setIsReady(TRUE)
      );
  }
  public function testSomethign() {
  
    // turn it on and off in a test.
    FeatureSwitches::get('mailchimp')->setIsLive($feature_switch);
    
  }
```
