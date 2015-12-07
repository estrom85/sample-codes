/* 
 * File:   BitConverter.h
 * Author: mato
 *
 * Created on Sobota, 2013, december 14, 10:56
 */

#ifndef BITCONVERTER_H
#define	BITCONVERTER_H

#include <string>
#include <sstream>

using namespace std;
typedef unsigned int uint;
typedef unsigned char byte;

class BitConverter {
public:
    static int readInt(byte* src, uint bytes = 4, uint offset = 0);
    static void writeInt(byte* dest, int number, uint bytes = 4, uint offset = 0);
    
    static uint readUInt(byte* src, uint bytes = 4, uint offset = 0);
    static void writeUInt(byte* dest, uint number, uint bytes=4, uint offset = 0);
    
    static string readString(byte* src, int length, int offset = 0);
    static void writeString(byte* dest, string src, int length, int offset = 0);
private:
    BitConverter();
    
    static bool isBigEndian();
};

#endif	/* BITCONVERTER_H */

