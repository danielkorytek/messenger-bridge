## Middlewares

#### Routing key middleware
That middleware gives an ability to routing key modification ie: add current locale key to current message key.

`RoutingKeyMiddleware` is implementing `RoutingKeyResolverInterface`.

Configuration:

- Register middleware

 ```
messenger.bridge.routing.app_id_routing_key_resolver:
    class: DanielKorytek\MessengerBridgeBundle\Message\Routing\RoutingKeyResolver\AppIdRoutingKeyResolver

Symfony < 5.1
messenger.bridge.middleware.routing_key:
    class: DanielKorytek\MessengerBridgeBundle\Middleware\RoutingKeyMiddleware
    arguments:
      - '@messenger.bridge.routing.app_id_routing_key_resolver'
    tags:
      - { name: messenger.middleware }

Symfony >= 5.1

messenger.bridge.middleware.routing_key:
    class: DanielKorytek\MessengerBridgeBundle\Middleware\Symf51\RoutingKeyMiddleware
    arguments:
      - '@messenger.bridge.routing.app_id_routing_key_resolver'
    tags:
      - { name: messenger.middleware }

```

- Attach a middleware to messenger

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
                       - messenger.bridge.middleware.routing_key
```


## Serialization
For serialize/deserialize messages package is using `symfony/serializer`. 
To endure adequate possibility for serialization/deserialization process, you need enable normalizers and denormalizers.

Required serializer configuration:

```
services:
    serializer.property.normalizer:
        class: Symfony\Component\Serializer\Normalizer\PropertyNormalizer
        arguments:
            - '@serializer.mapping.class_metadata_factory'
            - '@serializer.name_converter.metadata_aware'
            - '@property_info'
            - '@serializer.mapping.class_discriminator_resolver'
            - null
            - []
        tags:
            - { name: serializer.normalizer }

    serializer.date_time.normalizer:
        class: Symfony\Component\Serializer\Normalizer\DateTimeNormalizer
        tags:
            - { name: serializer.normalizer }

    serializer.array_denormalizer:
        class: Symfony\Component\Serializer\Normalizer\ArrayDenormalizer
        tags:
            - { name: serializer.denormalizer }
```


### Bus example configuration

```
framework:
    messenger:
        #your other buses here
        buses:
            shared.message.bus:
                default_middleware: allow_no_handlers
                middleware:
                    #here other middlewares like doctorine transaction etc.
                    - messenger.bridge.middleware.routing_key
        #here is global bus serializer configuration
        serializer:
            default_serializer: messenger.transport.symfony_serializer
            symfony_serializer:
                format: json
                context: { }
        #here is transport configuration
        transports:
            #other transports here
            shared: #configuration of outgoing exchange (messenger is publishing messages here to other apps)
                serializer: messenger.bridge.serializer #our custom serializer for correct serialization/deserialization shared event messages 
                dsn: "%env(MESSENGER_TRANSPORT_DSN)%" #dsn to rabbitmq
                options:
                    exchange:
                    name: "%env(MESSENGER_SHARED_EXCHANGE_NAME)%" #outgoing exchange name 
                    type: topic
            
            incoming:
                 serializer: messenger.bridge.serializer
                 dsn: "%env(MESSENGER_TRANSPORT_DSN)%"
                 options:
                    queues:
                        incoming_events: #your queue name (here yours app receives messages from other apps via exchange)
                            binding_keys:
                                - "%locale%.app-name.smth.changed" #routing key binding list
                    exchange:
                        name: "%env(MESSENGER_SHARED_INCOMING_EXCHANGE_NAME)%" #incoming exchange name
                        type: topic
```

### Messages

All messages representation classes `HAVE TO` implement `NamedMessageInterface`. Every message handled by bus, without implementing `NamedMessageInterface` will throws `UnsupportedMessage` exception.
 
### Publishing

1. Check if your bus has `allow_no_handlers` middleware (it's required). Your bus will publish message to transport only when you don't have any subscriber class for that message.
2. Add your message FQCN to messenger routing configuration with correct transport name:
   ```
      routing:
               'App\SharedEventBus\Test': shared
   ```

### Subscribing 

- Add message binding key in your incoming transport:
This step is for messenger command. If you're using different package, you should update config in proper place.
```
 incoming:
    serializer: messenger.bridge.serializer
    dsn: "%env(MESSENGER_TRANSPORT_DSN)%"
    options:
        queues:
            incoming_events: #your queue name (here yours app receives messages from other apps via exchange)
                binding_keys:
                    - "%locale%.app-name.smth.changed"
                    - "%locale%.app-name.smth"  #new key
                exchange:
                    name: "%env(MESSENGER_SHARED_INCOMING_EXCHANGE_NAME)%" #incoming exchange name
                    type: topic
```
- Create representation class for a message.
- Attach message mapping to `messenger.bridge.subscriber_events_mapping` parameter:

```
messenger.bridge.subscriber_events_mapping:
    app-name.smth.changed: App\Namespace\MessageClass
```
- Create subscriber class and bind with correct bus: 

```
<?php

declare(strict_types=1);

namespace App\SharedEventBus;


use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class SaasAttendanceSubscriber implements MessageSubscriberInterface
{
    public function __invoke(MessageClass $messageClass): Envelope
    {
        return new Envelope($messageClass);
    }

    public static function getHandledMessages(): iterable
    {
        yield MessageClass::class => [
            'bus' => 'shared.message.bus',
        ];
    }

}

```
