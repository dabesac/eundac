eUndac.Collections.UserPayments = Backbone.Collection.extend({
	url : '/rest/userpayment',
	model : eUndac.Models.UserPayment
});