/* 
 * File:   InternalFileHandler.cpp
 * Author: mato
 * 
 * Created on Sobota, 2013, december 14, 11:54
 */

#include <vector>

#include "InternalFileHandler.h"

InternalFileHandler::InternalFileHandler(string source, ifstream& in) {
    _source = source;
    _extracted = false;
    readHeaderInfo(in);
    setTempDirectory("");
}

InternalFileHandler::InternalFileHandler(string source, unsigned int offset) {
    _source = source;
    _extracted = false;
    ifstream in(source.c_str(),ios_base::in|ios_base::binary);
    if(!in.good()){
        this->readHeaderInfo(in);
    }else{
        in.seekg(offset,ifstream::beg);
        this->readHeaderInfo(in);
    }
    in.close();
    setTempDirectory("");
}

InternalFileHandler::~InternalFileHandler() {
    if(_extracted){
        remove(this->getFilePath().c_str());
    }
}

void InternalFileHandler::readHeaderInfo(ifstream& in){
    if(!in.good()){
        _filename = "";
        _dataType = 0;
        _fileSize = 0;
        _fileOffset = 0;
        _extracted = false;
        _valid = false;
        return;
    }
    char* buffer = new char[8];
    /* 0 - 7:   (8B) Key */
    in.read(buffer,8);
    this->setKey(BitConverter::readString((byte*)buffer,8));
    /* 8 - 11:  (4B) File length */
    in.read(buffer,4);
    _fileSize = BitConverter::readUInt((byte*)buffer,4);
    /* 12 - 15: (4B) Data type */
    in.read(buffer,4);
    _dataType = BitConverter::readUInt((byte*)buffer,4);
    /* 16 - 19: (4B) Filename length */
    in.read(buffer,4);
    unsigned int filename_length = BitConverter::readUInt((byte*)buffer,4);
    /* 20 - ?:  (?B) Filename string */
    delete[] buffer;
    buffer = new char[filename_length];
    in.read(buffer,filename_length);
    _filename = BitConverter::readString((byte*)buffer,filename_length);
    /* (4B) file position*/
    in.read(buffer,4);
    _fileOffset = BitConverter::readUInt((byte*)buffer,4);
    delete[] buffer;
    _valid = true;
}

string InternalFileHandler::getFileName(){
    return _filename;
}

unsigned int InternalFileHandler::getDataType(){
    return _dataType;
}

unsigned int InternalFileHandler::getFileLength(){
    return _fileSize;
}

void InternalFileHandler::writeFileData(ofstream& out){
    if(!isValid())
        return;
    string src;
    int offset;
    if(_extracted){
        src = _path;
        offset = 0;
    }else{
        src = _source;
        offset=_fileOffset;
    }
    ifstream in(src.c_str(),ios_base::in|ios_base::binary);
    in.seekg(offset,ifstream::beg);
    
    int buffer_size = 1024*1024;
    char* buffer = new char[buffer_size];
    int remaining = _fileSize;
    int toCopy = 0;
    
    while(in.good()&&!in.eof()&&remaining>0){
        toCopy = (remaining>buffer_size)?buffer_size:remaining;
        in.read(buffer,toCopy);
        out.write(buffer,toCopy);
        remaining -= toCopy;
    }
    
    delete [] buffer;
}

string InternalFileHandler::getFilePath(){
    if(!_extracted){
        FileHandler::writeFileData(_path);
        _extracted = true;
    }
    return _path;
}

bool InternalFileHandler::isValid(){
    return _valid;
}

void InternalFileHandler::setTempDirectory(string dir){
    int length = dir.length();
    int pos = dir.find_last_of("/\\");
    char type;
    if(pos==string::npos){
        type = '/';
    }else{
        type = dir[pos];
    }
    if(dir[length-1]=='/'||dir[length-1]=='\\'){
        _tempDir = dir.substr(0,length-1);
    }else{
        _tempDir = dir;
    }
    if(_tempDir.empty()){
        _path = _filename;
    }else{
       _path =  _tempDir + type + _filename;
    }
}