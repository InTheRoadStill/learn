/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2018 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_cheng.h"
#include <stdio.h>  
#include <stdlib.h>  
#include <string.h>  
#include <errno.h>  
#include <unistd.h>  
#include <netdb.h>  
#include <net/if.h>
#include <arpa/inet.h>
#include <sys/ioctl.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#define MAC_SIZE    18  
#define IP_SIZE     16 
#define MAXLINE     999

int send_http(char *content); //发送内容
/* If you declare any globals in php_cheng.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(cheng)
*/

/* True global resources - no need for thread safety here */
static int le_cheng;

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("cheng.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_cheng_globals, cheng_globals)
    STD_PHP_INI_ENTRY("cheng.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_cheng_globals, cheng_globals)
PHP_INI_END()
*/
/* }}} */

/* Remove the following function when you have successfully modified config.m4
   so that your module can be compiled into PHP, it exists only for testing
   purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_cheng_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_cheng_compiled)
{
	char *arg = NULL;
	size_t arg_len, len;
	zend_string *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	strg = strpprintf(0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "cheng", arg);

	RETURN_STR(strg);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and
   unfold functions in source code. See the corresponding marks just before
   function definition, where the functions purpose is also documented. Please
   follow this convention for the convenience of others editing your code.
*/
PHP_FUNCTION(cheng_test)
{
	zend_long arg1, arg2=0;
	size_t arg1_len, arg2_len, len;
	zend_string *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "l|l", &arg1, &arg2) == FAILURE) {
		return;
	}
	zend_long result;
	php_printf("%d\r\n", arg2);
	result = arg1 + arg2;

	RETURN_LONG(result);
}

PHP_FUNCTION(get_mac)
{
	char mac[MAC_SIZE];
	zend_string *strg;
	const char *test_eth = "eth0";
	get_local_mac(test_eth, mac);
	strg = strpprintf(0, mac);
	RETURN_STR(strg);
}

PHP_FUNCTION(get_bytes)
{
	
}

PHP_FUNCTION(str_add)
{
	char *arg1 = NULL;
	char *arg2 = "";
	size_t arg1_len, arg2_len, len;
	zend_string *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "s|s", &arg1, &arg1_len, &arg2, &arg2_len) == FAILURE) {
		return;
	}
	send_http(arg1);
	strg = strpprintf(0,"%s%s1" , arg1, arg2);


	RETURN_STR(strg);
}

PHP_FUNCTION(array_add)
{
	zval *arr, *entry, value;

	zend_string *string_key, *result;
	zend_ulong num_key;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "a", &arr) == FAILURE) {
        return;
    }

    array_init_size(return_value, zend_hash_num_elements(Z_ARRVAL_P(arr)));

    ZEND_HASH_FOREACH_KEY_VAL(Z_ARRVAL_P(arr), num_key, string_key, entry) {

    	if(Z_TYPE_P(entry) == IS_STRING){
    		result = strpprintf(0, "%s1", Z_STRVAL_P(entry));
    		ZVAL_STR(&value, result);
	    	if(string_key){
	        	zend_hash_index_update(Z_ARRVAL_P(return_value), string_key, &value);
	    	}else{
	    		zend_hash_index_update(Z_ARRVAL_P(return_value), num_key, &value);
	    	}
    	}else{
    		// result = strpprintf(0, "%s1", Z_STRVAL_P(entry));
    		zend_hash_index_update(Z_ARRVAL_P(return_value), num_key, entry);
    	}



    }ZEND_HASH_FOREACH_END();
}

// 获取本机mac  
int get_local_mac(const char *eth_inf, char *mac)  
{  
    struct ifreq ifr;  
    int sd;  
      
    bzero(&ifr, sizeof(struct ifreq));  
    if( (sd = socket(AF_INET, SOCK_STREAM, 0)) < 0)  
    {  
        printf("get %s mac address socket creat error\n", eth_inf);  
        return -1;  
    }  
      
    strncpy(ifr.ifr_name, eth_inf, sizeof(ifr.ifr_name) - 1);  
  
    if(ioctl(sd, SIOCGIFHWADDR, &ifr) < 0)  
    {  
        printf("get %s mac address error\n", eth_inf);  
        close(sd);  
        return -1;  
    }  
  
    snprintf(mac, MAC_SIZE, "%02x:%02x:%02x:%02x:%02x:%02x",  
        (unsigned char)ifr.ifr_hwaddr.sa_data[0],   
        (unsigned char)ifr.ifr_hwaddr.sa_data[1],  
        (unsigned char)ifr.ifr_hwaddr.sa_data[2],   
        (unsigned char)ifr.ifr_hwaddr.sa_data[3],  
        (unsigned char)ifr.ifr_hwaddr.sa_data[4],  
        (unsigned char)ifr.ifr_hwaddr.sa_data[5]);  
  
    close(sd);  
      
    return 0;  
}

int send_http(char *content)
{
    int sockfd,n;  
    char recvline[MAXLINE];  
    struct sockaddr_in servaddr;  
    char dns[32];  
    char url[128];  
    char *IP = "192.168.4.220";
    char *buf;
    char *buf1 = "GET /test/index.php?mac=%s HTTP/1.1\r\n\
Host: 192.168.4.220\r\n\
Proxy-Connection: keep-alive\r\n\
Cache-Control: max-age=0\r\n\
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3\r\n\
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36\r\n\
Accept-Encoding: gzip, deflate\r\n\
Accept-Language: zh-CN,zh;q=0.9\r\n\
\r\n";
	buf = (char*)malloc(sizeof(char)*printf(buf1, content));//申请sizeof(char)*n个大小的内存空间
    sprintf(buf, buf1, content);
    if((sockfd = socket(AF_INET,SOCK_STREAM,0)) < 0)  
        printf("socket error\n");  
    printf("1\n");  
    bzero(&servaddr,sizeof(servaddr));  
    servaddr.sin_family = AF_INET;  
    servaddr.sin_port = htons(80);  
    if(inet_pton(AF_INET,IP,&servaddr.sin_addr) <= 0)  
        printf("inet_pton error\n");  
    if(connect(sockfd,(struct sockaddr *)&servaddr,sizeof(servaddr)) < 0)  
        printf("connect error\n");  
    write(sockfd,buf,strlen(buf));
    while((n = read(sockfd,recvline,MAXLINE)) > 0)  
    {  
        recvline[n] = 0;  
        if(fputs(recvline,stdout) == EOF)  
            printf("fputs error\n");  
    }  
    if(n < 0)  
        printf("read error\n");  
    printf("all ok now\n");  
    return 0;
}

/* {{{ php_cheng_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_cheng_init_globals(zend_cheng_globals *cheng_globals)
{
	cheng_globals->global_value = 0;
	cheng_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(cheng)
{
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(cheng)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(cheng)
{
#if defined(COMPILE_DL_CHENG) && defined(ZTS)
	ZEND_TSRMLS_CACHE_UPDATE();
#endif
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(cheng)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(cheng)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "cheng support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */

/* {{{ cheng_functions[]
 *
 * Every user visible function must have an entry in cheng_functions[].
 */
const zend_function_entry cheng_functions[] = {
	PHP_FE(confirm_cheng_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE(cheng_test,	NULL)
	PHP_FE(str_add,	NULL)
	PHP_FE(array_add, NULL)
	PHP_FE(get_mac, NULL)
	PHP_FE_END	/* Must be the last line in cheng_functions[] */
};
/* }}} */

/* {{{ cheng_module_entry
 */
zend_module_entry cheng_module_entry = {
	STANDARD_MODULE_HEADER,
	"cheng",
	cheng_functions,
	PHP_MINIT(cheng),
	PHP_MSHUTDOWN(cheng),
	PHP_RINIT(cheng),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(cheng),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(cheng),
	PHP_CHENG_VERSION,
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_CHENG
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(cheng)
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
