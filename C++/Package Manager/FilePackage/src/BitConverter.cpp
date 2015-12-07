/* 
 * File:   BitConverter.cpp
 * Author: mato
 * 
 * Created on Sobota, 2013, december 14, 10:56
 */

#include "BitConverter.h"

BitConverter::BitConverter() {
}

bool BitConverter::isBigEndian(){
    uint dummy = 1;
    byte* bytes = (byte*)(&dummy);
    return bytes[0]!=1;
}

int BitConverter::readInt(byte* src, uint bytes, uint offset){
    int out = 0;
    
    uint bytesMax = (bytes>sizeof(out))?sizeof(out):bytes;
    uint byteOffset = sizeof(out) - bytesMax;
    
    byte* out_bytes = (byte*)(&out);
    for(int i = 0; i < bytesMax; i++){
        if(BitConverter::isBigEndian()){
            out_bytes[i+byteOffset] = src[bytesMax - i - 1 + offset];
        }else{
            out_bytes[i] = src[i+offset];
        }
    }
    return out;
}

void BitConverter::writeInt(byte* dest, int number, uint bytes, uint offset){
    byte* in_bytes = (byte*)(&number);
    uint bytesMax = (bytes>sizeof(number))?sizeof(number):bytes;
    uint byteOffset = sizeof(number) - bytesMax;
    
    for(int i = 0; i < bytesMax; i++){
        if(BitConverter::isBigEndian()){
            dest[bytesMax - i - 1 + offset] =  in_bytes[i+byteOffset];
        }else{
            dest[i+offset] = in_bytes[i];
        }
    }
}

uint BitConverter::readUInt(byte* src, uint bytes, uint offset){
    uint out = 0;
    
    uint bytesMax = (bytes>sizeof(out))?sizeof(out):bytes;
    uint byteOffset = sizeof(out) - bytesMax;
    
    byte* out_bytes = (byte*)(&out);
    for(int i = 0; i < bytesMax; i++){
        if(BitConverter::isBigEndian()){
            out_bytes[i+byteOffset] = src[bytesMax - i - 1 + offset];
        }else{
            out_bytes[i] = src[i+offset];
        }
    }
    return out;
}

void BitConverter::writeUInt(byte* dest, uint number, uint bytes, uint offset){
    byte* in_bytes = (byte*)(&number);
    uint bytesMax = (bytes>sizeof(number))?sizeof(number):bytes;
    uint byteOffset = sizeof(number) - bytesMax;
    
    for(int i = 0; i < bytesMax; i++){
        if(BitConverter::isBigEndian()){
            dest[bytesMax - i - 1 + offset] =  in_bytes[i+byteOffset];
        }else{
            dest[i+offset] = in_bytes[i];
        }
    }
}

string BitConverter::readString(byte* src, int length, int offset){
    string out;
    char c;
    for(int i = 0;i<length;i++){
        c = (char)src[i+offset];
        if(c!=0)
            out.push_back(c);
    }
    return out;
}

void BitConverter::writeString(byte* dest, string src, int length, int offset){
    for(int i=0;i<length;i++){
        if(i<src.length()){
            dest[i+offset] = (byte)src[i];
        }else{
            dest[i+offset] = 0;
        }
    }
}