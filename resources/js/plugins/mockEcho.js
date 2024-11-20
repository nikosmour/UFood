export class MockEcho {
	constructor() {
		return new Proxy( this, {
			get( target, prop ) {
				// Return a function for any property access
				if ( typeof target[ prop ] === "undefined" ) {
					return ( ...args ) => {
						console.warn( `MocEcho Called method: ${ prop } with arguments:`, args );
						return target; // Always return the instance
					};
				}
				return target[ prop ];
			},
		} );
	}

	/**
	 * Simulates subscribing to a public channel.
	 * @param {string} channel - The name of the public channel.
	 * @returns {MockChannel}
	 */
	channel( channel ) {
		console.warn( `[MockEcho] Subscribed to channel: ${ channel }` );
		return new MockChannel( channel );
	}
	
	/**
	 * Simulates subscribing to a presence channel.
	 * @param {string} channel - The name of the presence channel.
	 * @returns {MockChannel}
	 */
	join( channel ) {
		console.warn( `[MockEcho] Joined presence channel: ${ channel }` );
		return new MockChannel( channel );
	}
	
	/**
	 * Mock method to simulate leaving a channel.
	 * @param {string} channel - The name of the channel to leave
	 * @returns {MockEcho} Returns the MockEcho instance itself.
	 */
	leave( channel ) {
		console.warn( `[MockEcho] Left channel: ${ channel }` );
		return this;
	}
	
	/**
	 * Mock method to simulate leaving a channel.
	 * @param {string} channel - The name of the channel to leave
	 * @returns {MockEcho} Returns the MockEcho instance itself.
	 */
	leaveAllChannels( channel ) {
		console.warn( `[MockEcho] Left channel: ${ channel }` );
		return this;
	}
	
	/**
	 * Simulates subscribing to a private channel.
	 * @param {string} channel - The name of the private channel.
	 * @returns {MockChannel}
	 */
	private( channel ) {
		console.warn( `[MockEcho] Subscribed to private channel: ${ channel }` );
		return new MockChannel( channel );
	}
	
	/**
	 * Mock method to simulate the socketId retrieval.
	 * @returns {null} Always returns null as there is no active Echo instance.
	 */
	socketId() {
		return null;
	}
}

class MockChannel {
	constructor( channelName ) {
		this.channelName = channelName;
		console.warn( `[MockChannel] Initialized for channel: ${ channelName }` );
		return new Proxy( this, {
			get( target, prop ) {
				// Return a function for any property access
				if ( typeof target[ prop ] === "undefined" ) {
					return ( ...args ) => {
						console.warn( `MockChannel Called method: ${ prop } with arguments:`, args );
						return target; // Always return the instance
					};
				}
				return target[ prop ];
			},
		} );
	}
	
	/**
	 * Simulates listening for an event.
	 * @param {string} event - The name of the event to listen for.
	 * @param {function} callback
	 * @returns {MockChannel}
	 */
	listen( event, callback ) {
		console.warn( `[MockChannel] Listening for event: ${ event } on channel: ${ this.channelName }` );
		return this;
	}
	
	/**
	 * Simulates stopping listening to an event.
	 * @param {string} event - The name of the event to stop listening for.
	 * @returns {MockChannel}
	 */
	stopListening( event ) {
		console.warn( `[MockChannel] Stopped listening for event: ${ event } on channel: ${ this.channelName }` );
		return this;
	}
	
	/**
	 * Simulates unsubscribing from the channel.
	 */
	unsubscribe() {
		console.warn( `[MockChannel] Unsubscribed from channel: ${ this.channelName }` );
	}
}
