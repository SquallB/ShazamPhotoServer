/* 
 * File:   base64.h
 * Author: carlo_000
 *
 * Created on 18 mars 2015, 14:55
 */

#ifndef BASE64_H	
#define	BASE64_H
#include <string>
#include <iostream>
std::string mEncode(std::string const& toEncode);
std::string blockEncode(std::string const& block);
unsigned char* mDecode(std::string const& s);
void blockDecode(std::string const& block);
#endif	/* BASE64_H */
