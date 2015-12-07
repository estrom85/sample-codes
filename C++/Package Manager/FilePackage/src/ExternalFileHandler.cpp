/* 
 * File:   ExternalFileHandler.cpp
 * Author: mato
 * 
 * Created on Pondelok, 2013, december 9, 18:38
 */

#include "ExternalFileHandler.h"

/*Constructors*/
ExternalFileHandler::ExternalFileHandler(string key,string path) {
    _path = path;
    FileHandler::setKey(key);
    int pos = path.find_last_of("/\\");
    if(pos == string::npos){
        _fileName=path;
    }else{
        _fileName=path.substr(pos+1);
    }
    ifstream in(path.c_str(),ios_base::in|ios_base::binary);
    if(!in.good()){
        _valid = false;
        _fileSize = 0;
        _dataType = 0;
        return;
    }
    in.seekg(0,ifstream::end);
    _fileSize = in.tellg();
    
    _dataType = getDataType(_fileName);
    _valid = true;  
}

ExternalFileHandler::~ExternalFileHandler() {
}

/*Reimplemented abstract function*/

string ExternalFileHandler::getFileName(){
    return _fileName;
}

unsigned int ExternalFileHandler::getDataType(){
    return _dataType;
}

unsigned int ExternalFileHandler::getFileLength(){
    return _fileSize;
}

void ExternalFileHandler::writeFileData(ofstream& out){
    if(!isValid())
        return;
    
    ifstream in(_path.c_str(),ios_base::in|ios_base::binary);
    
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

string ExternalFileHandler::getFilePath(){
    return _path;
}

bool ExternalFileHandler::isValid(){
    return _valid;
}
/*Helpers*/
unsigned int ExternalFileHandler::getDataType(string filename){
    string ext;
    int delim = filename.find_last_of('.');
    if(delim == filename.npos)
        ext = filename;
    else
        ext = filename.substr(delim+1);
    ext = strToLower(ext);   
    
    if(contains(ext,"mp3;wav;ogg;raw")){
        return FileHandler::DATA_TYPE_AUDIO;
    }else if(contains(ext,"jpg;jpeg;gif;tif;tiff;png;bmp")){
        return FileHandler::DATA_TYPE_IMAGE;
    }else if(contains(ext,"ttf")){
        return FileHandler::DATA_TYPE_FONT;
    }else{
        return FileHandler::DATA_TYPE_UNKNOWN;
    }
}

string ExternalFileHandler::strToLower(string& str){
    stringstream ss;
    ss.clear();
    for(string::iterator i=str.begin();i!=str.end();i++){
        ss<<(char)tolower(*i);
    }
    return ss.str();
}

bool ExternalFileHandler::contains(string& str, string strList){
    /*split*/
    //set<string> stringset;
    stringstream ss;
    ss.clear();
    for(string::iterator i=strList.begin();i!=strList.end();i++){
        if((*i)==';'){
            //stringset.insert(ss.str());
            if(ss.str()==str){
                return true;
            }
            ss.str(string());
        }else{
            ss<<*i;
        }
    }
    if(ss.str()==str){
        return true;
    }
    return false;
}