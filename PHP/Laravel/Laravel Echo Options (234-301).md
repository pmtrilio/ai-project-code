 * Specifies the broadcaster
 */
export type Broadcaster = {
    reverb: {
        connector: PusherConnector<'reverb'>;
        public: PusherChannel<'reverb'>;
        private: PusherPrivateChannel<'reverb'>;
        encrypted: PusherEncryptedPrivateChannel<'reverb'>;
        presence: PusherPresenceChannel<'reverb'>;
    };
    pusher: {
        connector: PusherConnector<'pusher'>;
        public: PusherChannel<'pusher'>;
        private: PusherPrivateChannel<'pusher'>;
        encrypted: PusherEncryptedPrivateChannel<'pusher'>;
        presence: PusherPresenceChannel<'pusher'>;
    };
    'socket.io': {
        connector: SocketIoConnector;
        public: SocketIoChannel;
        private: SocketIoPrivateChannel;
        encrypted: never;
        presence: SocketIoPresenceChannel;
    };
    null: {
        connector: NullConnector;
        public: NullChannel;
        private: NullPrivateChannel;
        encrypted: NullEncryptedPrivateChannel;
        presence: NullPresenceChannel;
    };
    function: {
        connector: any;
        public: any;
        private: any;
        encrypted: any;
        presence: any;
    };
};

type Constructor<T = {}> = new (...args: any[]) => T;

export type BroadcastDriver = Exclude<keyof Broadcaster, 'function'>;

export type EchoOptions<TBroadcaster extends keyof Broadcaster> = {
    /**
     * The broadcast connector.
     */
    broadcaster: TBroadcaster extends 'function'
        ? Constructor<InstanceType<Broadcaster[TBroadcaster]['connector']>>
        : TBroadcaster;

    auth?: {
        headers: Record<string, string>;
    };
    authEndpoint?: string;
    userAuthentication?: {
        endpoint: string;
        headers: Record<string, string>;
    };
    csrfToken?: string | null;
    bearerToken?: string | null;
    host?: string | null;
    key?: string | null;
    namespace?: string | false;

    [key: string]: any;
};