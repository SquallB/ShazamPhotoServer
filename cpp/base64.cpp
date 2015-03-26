#include "base64.h"
using namespace std;
static const string base64_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
static char  decodedBlock[3];
string mEncode(string const& toEncode) {
    int expectedSize = toEncode.size();
    
    
    string base64Str = "";
    if (toEncode.size() == 0) {
        return base64Str;
    } else {
        while ((expectedSize % 3) != 0) {
            
            expectedSize++;
        }

        int i = 0;
        for (i = 0; i < toEncode.size(); i += 3) {
            string block = toEncode.substr(i, 3);
            string encodedBlock=blockEncode(block);
            base64Str.append(encodedBlock);
            
        }
        if(toEncode.size()<expectedSize){
            string lastBlock = "";
            for (i; i < expectedSize; i++) {
                if (i < toEncode.size()) {
                    lastBlock.append(&toEncode[i]);
                }else{
                    
                    lastBlock.append("0");
                }
            }
            
            string encodedBlock=blockEncode(lastBlock);
            base64Str.append(encodedBlock);
        }
    }
    return base64Str;
}

string blockEncode(string const& block) {
    
    unsigned char mask1 = 0b00000011;
    unsigned char mask2 = 0b11110000;
    unsigned char mask3 = 0b00001111;
    unsigned char mask4 = 0b11000000;
    
    int b64Index[4];
    b64Index[0] = block[0]>>2;
    b64Index[1] = ((block[0]&mask1)<<4)xor((block[1]&mask2)>>4);
    b64Index[2] = ((block[1]&mask3)<<2)xor((block[2]&mask4)>>6);
    b64Index[3] = block[2]&(~mask4);
    
    
    char myChar[5] = {base64_chars[b64Index[0]],base64_chars[b64Index[1]],base64_chars[b64Index[2]],base64_chars[b64Index[3]]};
   
    string encodedBlock =  string(myChar);
    return encodedBlock;
}

unsigned char* mDecode(string const& s) {
    unsigned char* decoded = new unsigned char[((s.size()*3) / 4) + 1];
    
    for (int i = 0; i < s.size(); i += 4) {
        int j = (i * 3) / 4;
        blockDecode(s.substr(i, 4));

        decoded[j] = (unsigned char) decodedBlock[0];
        decoded[j + 1] = (unsigned char) decodedBlock[1];
        decoded[j + 2] = (unsigned char) decodedBlock[2];
    }
    
    return decoded;
}

void blockDecode(string const& block) {
    unsigned char mask1 = 0b00000011;
    unsigned char mask2 = 0b00111100;
    unsigned char mask3 = 0b00001111;
    unsigned char mask4 = 0b00110000;

    decodedBlock[0] = (char) (base64_chars.find(block[0]) << 2)+((base64_chars.find(block[1])&(mask4)) >> 4);
    decodedBlock[1] = (char) ((base64_chars.find(block[1])&(mask3)) << 4)+((base64_chars.find(block[2]) & mask2) >> 2);
    decodedBlock[2] = (char) ((base64_chars.find(block[2])&(mask1)) << 6)+(base64_chars.find(block[3]));
}
