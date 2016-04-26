/**
 * 目的是真实模拟后端的页面路由请求，提供出前端可仿真的功能
 * @author Finrila finrila@gmail.com
 */
var fs = require("fs");
var path = require("path");
var jade = require('jade');

module.exports = function(connect, options) {
  
  return function(req, res, next) {
  
	if (!req.__back_requrest__) {
		return next();
	}

	var __real_url__ = req.__real_url__.replace(/\?.*?$/, '');
	
	var filePath = path.join(process.cwd(), req.url.replace(/\?.*?$/, ''));
	
	if (!/\/$/.test(__real_url__)) {
		if (fs.existsSync(filePath)) {
			if (/\.jade$/.test(__real_url__)) {
				return responseJade(filePath);
			}
		} else if (fs.existsSync(filePath + '.html')) {
			req.url = req.url + '.html';
		} else if (fs.existsSync(filePath + '.jade')) {
			return responseJade(filePath + '.jade');
		}
	}
	next();
	
	function responseJade(filePath) {
		var compiled = jade.compile(fs.readFileSync(filePath, 'utf-8'), {
			filename: filePath + '.jade',
			pretty: true
		})();
		res.end(compiled);
	}
  };

};