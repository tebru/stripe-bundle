<?php
/*
 * Copyright (c) 2015 Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\StripeBundle\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializerBuilder;
use Tebru\Retrofit\Adapter\Rest\RestAdapter;
use Tebru\Stripe\Serializer\SourceSubscriber;

/**
 * Class StripeRestAdapterFactory
 *
 * @author Nate Brunette <n@tebru.net>
 */
class StripeRestAdapterFactory
{
    public static function make($apiKey, $baseUrl, ClientInterface $client = null, SerializerBuilder $serializerBuilder = null)
    {
        if (null === $client) {
            $client = new Client(['exceptions' => false, 'auth' => [$apiKey, null]]);
        }

        if (null === $serializerBuilder) {
            $serializerBuilder = new SerializerBuilder();
        }

        $serializerBuilder->addDefaultListeners();
        $serializerBuilder->configureListeners(
            function(EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new SourceSubscriber());
            }
        );

        $serializer = $serializerBuilder->build();

        return RestAdapter::builder()
            ->setBaseUrl($baseUrl)
            ->setHttpClient($client)
            ->setSerializer($serializer)
            ->build();
    }
}
