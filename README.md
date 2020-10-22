



## Middlewares

#### Routing key middleware
That middleware gives an ability to routing key modification ie: add current locale key to current message key.

`RoutingKeyMiddleware` is implementing `RoutingKeyResolverInterface`.

Configuration:

1. Register middleware
 ```
messenger.bridge.routing.app_id_routing_key_resolver:
    class: DanielKorytek\MessengerBridgeBundle\Message\Routing\RoutingKeyResolver\AppIdRoutingKeyResolver

messenger.bridge.middleware.routing_key:
    class: DanielKorytek\MessengerBridgeBundle\Middleware\RoutingKeyMiddleware
    arguments:
      - '@messenger.bridge.routing.app_id_routing_key_resolver'
    tags:
      - { name: messenger.middleware }
```

2. Attach middleware to messenger

In `messenger.yml` put `messenger.bridge.middleware.routing_key` to your bus middleware list.

Example:

```
framework:
       messenger:
           default_bus: command.bus
           buses:
               command.bus: ~
               shared.message.bus:
                   default_middleware: allow_no_handlers
                   middleware:
                       - messenger.bridge.middleware.routing_key```
