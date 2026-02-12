                'transport' => $transportName,
                'message_id' => $envelope->last(TransportMessageIdStamp::class)?->getId(),
            ]);
            $this->receivers[$transportName]->keepalive($envelope, $seconds);
        }
    }

    public function getMetadata(): WorkerMetadata
    {
        return $this->metadata;
    }
}
