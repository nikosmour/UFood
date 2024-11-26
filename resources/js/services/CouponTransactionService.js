// CouponTransactionService.js


import CouponTransaction from "@models/CouponTransaction.js";

export default class CouponTransactionService {
	
	_tempBalances;
	_url;
	
	/**
	 * Initialize the service with necessary data.
	 * @param {Object} axios - Axios instance for making HTTP requests.
	 * @param {string} url - The initial URL to fetch transactions.
	 * @param {Object} enums - Enum containing meal plan periods.
	 * @param tempBalances
	 */
	constructor( axios, url, enums, tempBalances ) {
		this.axios = axios;
		this._url = url;
		this.enums = enums;
		this._tempBalances = new tempBalances.constructor( tempBalances );
	}
	
	/**
	 * Calculates and updates the meal balance for a specific transaction and meal type.
	 * @param {number} id - id of the transaction.
	 * @param {string} transactionType - Type of transaction.
	 * @returns {string} Updated meal balance.
	 */
	_calculateID( id, transactionType ) {
		let type = null;
		if ( transactionType === "buying" ) {
			type = "P";
		} else if ( transactionType === "sending" || transactionType === "receiving" ) {
			type = "T";
		} else if ( transactionType === "using" )
			type = "U";
		return type + id;
	}
	
	/**
	 * Calculates and updates the meal balance for a specific transaction and meal type.
	 * @param {number} amount - Quantity of meal involved.
	 * @param {string} transactionType - Type of transaction.
	 * @param {string} meal - Meal type to update (e.g., breakfast, lunch).
	 * @returns {number} Updated meal balance.
	 */
	_calculateMeal( amount, transactionType, meal ) {
		this._tempBalances[ meal ] -= amount;
		return this._tempBalances[ meal ];
	}
	
	/**
	 * Calculates the total money based on transaction type.
	 * @param {number} amount - Transaction amount.
	 * @param {string} transactionType - Type of transaction (e.g., buying, receiving).
	 * @returns {number} Updated money balance.
	 */
	_calculateMoney( amount, transactionType ) {
		if ( transactionType === "buying" || transactionType === "sending" ) {
			this._tempBalances.money += amount;
		} else if ( transactionType === "receiving" ) {
			this._tempBalances.money -= amount;
		}
		return this._tempBalances.money;
	}
	
	/**
	 * Fetches transaction data from the server and updates the transactions array.
	 * If there are more transactions to fetch, the next page URL is updated for future requests.
	 *
	 * @returns {Promise<Object>} A promise that resolves to an object containing:
	 *   @property {Array<Object>} transactions - Formatted transactions with calculated totals.
	 *   @property {boolean} stopFetch - Boolean indicating if there are more transactions to fetch.
	 *
	 * @throws {Error} Throws an error if the fetch request fails.
	 */
	async fetchTransactions() {
		if ( !this._url )
			return {
				transactions : [],
				stopFetch :    true,
			};
		
		const response = await this.axios.get( this._url );
		const transactions = response.data.transactions.data;
		this._url = response.data.transactions.next_page_url;
		return {
			transactions : this.reformatTransactions( transactions ),
			stopFetch :    !this._url,
		};
	}
	
	/**
	 * Formats transactions, updating id, meal and money totals based on transaction type.
	 * @param {Array<Object>} transactions - List of transactions to format.
	 * @returns {Array<Object>} Formatted transactions with calculated totals.
	 */
	reformatTransactions( transactions ) {
		return transactions.map( temp => {
			const transaction = new CouponTransaction( temp );
			transaction.totalMoney = this._calculateMoney( transaction.money, transaction.transaction );
			for ( const meal in this.enums.MealPlanPeriodEnum ) {
				
				/**
				 * for showing the balance before the transaction
				 */
				//transaction[`total.${meal}`] = this._calculateMeal(transaction[meal], transaction.transaction, meal);
				
				/**
				 * for showing the balance after the transaction
				 */
				transaction[ `total.${ meal }` ] = this._tempBalances[ meal ];
				this._calculateMeal( transaction[ meal ], transaction.transaction, meal );
			}
			return transaction;
		} );
	}
	
	/*
		/!**
		 * Calculates and updates the meal balance for a specific transaction and meal type
		 * if we choose to not do it on backed or DB
		 * @param {number} amount - Quantity of meal involved.
		 * @param {string} transactionType - Type of transaction.
		 * @param {boolean} isUsing - is Using the current meal only for transactionType === 'using'
		 * @returns {number} Updated meal balance.
		 *!/
		_calculateMealValue(amount,transactionType, isUsing) {
			if (transactionType === 'buying' || transactionType === 'receiving')
				return Number(amount);
			//else if (transactionType === 'sending' || transactionType === 'using')
			return -Number(amount);

		}
		*/
	
}
