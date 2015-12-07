/* 
 * File:   FileHandler.cpp
 * Author: mato
 * 
 * Created on Sobota, 2013, december 14, 13:50
 */

#include "FileHandler.h"

/* Constructors and destructors */
FileHandler::FileHandler(){
    _key="";
}

FileHandler::~FileHandler(){
    
}

void FileHandler::setKey(string key){
    if(key.length()>8){
        _key = key.substr(0,8);
    }else{
        _key = key;
    }
}

string FileHandler::getKey(){
    return _key;
}

unsigned int FileHandler::getHeaderSize(){
    unsigned int filename_length = this->getFileName().length();
    return 20+filename_length+1;
}

void FileHandler::writeHeader(ofstream& out){
    unsigned char* header = new unsigned char[getHeaderSize()];
    writeHeader(header);
    out.write((char*)header,getHeaderSize());
    delete[] header;
}

void FileHandler::writeHeader(unsigned char* byte_array, int offset){
    /* 0 - 7:   (8B) Key */
     BitConverter::writeString(byte_array,getKey(),8,offset);
     
     /* 8 - 11:  (4B) File length */
     BitConverter::writeUInt(byte_array,getFileLength(),4,8+offset);
     
     /* 12 - 15: (4B) Data type */
     BitConverter::writeUInt(byte_array,getDataType(),4,12+offset);
     
     /* 16 - 19: (4B) Filename length */
     BitConverter::writeUInt(byte_array,getFileName().length()+1,4,16+offset);
     
     /* 20 - ?:  (?B) Filename string */
     BitConverter::writeString(byte_array,getFileName(),getFileName().length()+1,20+offset); 
}

void FileHandler::writeFileData(string path){
    ofstream out(path.c_str(),ios_base::out|ios_base::binary|ios_base::trunc);
    if(out.good()){
        writeFileData(out);
    }
    out.close();
}