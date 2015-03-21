/* 
 * File:   base64.h
 * Author: carlo_000
 *
 * Created on 18 mars 2015, 14:55
 */

#ifndef BASE64_H
#define	BASE64_H
#include <string>

std::string base64_encode(unsigned char const* , unsigned int len);
std::string base64_decode(std::string const& s);


#endif	/* BASE64_H */

