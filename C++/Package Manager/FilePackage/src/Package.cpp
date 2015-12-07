/* 
 * File:   PackageHeader.cpp
 * Author: mato
 * 
 * Created on Sobota, 2013, december 7, 19:59
 */

#include <stdlib.h>

#include "Package.h"

Package::Package(char* tempDir) {
    _numberOfFiles = 0;
    _filename = "newPackage";
    _tempdir = tempDir;
}

Package::Package(string path, char* tempDir) {
    _numberOfFiles = 0;
    _filename = "newPackage";
    _tempdir = tempDir;
    
    ifstream in(path.c_str(), ios_base::in | ios_base::binary);

    if (!in.good())
        return;

    if (!this->readHeader(in)) {
        in.close();
        return;
    }
    InternalFileHandler* file;
    for (int i = 0; i < _numberOfFiles; i++) {
        file = new InternalFileHandler(path,in);
        file->setTempDirectory(_tempdir);
        addFileHandler(file);
    }
    in.close();
    
    int pos = path.find_last_of("/\\");
    if(pos==string::npos){
        _filename = path;
    }else{
        _filename = path.substr(pos+1);
    }
}

Package::~Package() {
    for (FILE_LIST::iterator i = _fileList.begin(); i != _fileList.end(); i++) {
        delete *i;
    }
}

const string& Package::getFileName() {
    return _filename;
}

bool Package::LoadFile(string path) {
    return LoadFile(generateKey(), path);
}

bool Package::LoadFile(string key, string path) {
    if (key.length() > 4)
        key = key.substr(0, 4);

    FileHandler* file = new ExternalFileHandler(key, path);

    if (_fileIndex.find(file->getFileName()) != _fileIndex.end()) {
        delete file;
        return false;
    }

    if (_fileDict.find(key) != _fileDict.end()) {
        delete _fileDict[key];
    }
    addFileHandler(file);
    this->_numberOfFiles++;
    return true;
}

void Package::addFileHandler(FileHandler* file) {
    _fileList.push_back(file);
    _fileDict[file->getKey()] = file;
    _fileIndex.insert(file->getFileName()); 
}

int Package::NumberOfFiles() {
    //return _numberOfFiles;
    return _fileDict.size();
}

FileHandler* Package::operator [](string& key) {
    return getFile(key);
}

FileHandler* Package::operator [](const char* key){
    return getFile(key);
}

FileHandler* Package::operator [](int index) {
    return getFile(index);
}

FileHandler* Package::getFile(int index){
    if (index >= _fileList.size())
        return 0;
    return _fileList[index];
}

FileHandler* Package::getFile(const char* key){
    string key_str = key;
    return getFile(key_str);
}

FileHandler* Package::getFile(string& key){
    if (_fileDict.find(key) == _fileDict.end()) {
        return 0;
    }
    return _fileDict[key];
}

void Package::ChangeKey(string oldKey, string newKey) {
    if (_fileDict.find(newKey) != _fileDict.end()) {
        return;
    }
    _fileDict[oldKey]->setKey(newKey);
    _fileDict[newKey] = _fileDict[oldKey];
    _fileDict.erase(oldKey);
}

void Package::RemoveFile(string key) {

    if (_fileDict.find(key) == _fileDict.end()) {
        return;
    }

    FileHandler* file = _fileDict[key];
    FILE_LIST::iterator i = _fileList.begin();
    while ((*i) != file && i != _fileList.end())i++;
    if (i == _fileList.end())
        return;
    _fileList.erase(i);
    _fileDict.erase(key);
    _fileIndex.erase(file->getFileName());
    delete file;
    _numberOfFiles--;
}

bool Package::SavePackage(string path) {
    for (FILE_LIST::iterator i = _fileList.begin(); i != _fileList.end(); i++) {
        (*i)->getFilePath(); //force generation of temp files
    }
    ofstream out(path.c_str(), ios_base::out | ios_base::binary | ios_base::trunc);
    if (!out.good())
        return false;
    /*Create file*/
    //write package header
    this->writeHeader(out);
    //write index file
    this->writeFileIndex(out);
    //write data
    this->writeData(out);

    out.close();

    int pos = path.find_last_of("/\\");
    if (pos == string::npos) {
        _filename = path;
    } else {
        _filename = path.substr(pos + 1);
    }

    return true;
}

void Package::writeHeader(ofstream& out) {
    char* data = new char[4];
    /* 0 - 3:  (4B) "PCK"*/
    BitConverter::writeString((byte*) data, "PCK", 4);
    out.write(data, 4);

    /* 4 - 7:  (4B) version */
    BitConverter::writeString((byte*) data, "0.01", 4);
    out.write(data, 4);

    /* 8 - 11: (4B) Number of files */
    BitConverter::writeUInt((byte*) data, NumberOfFiles(), 4);
    out.write(data, 4);

    delete[] data;
}

bool Package::readHeader(ifstream& in) {
    char* data = new char[4];
    /* 0 - 3:  (4B) "PCK"*/
    in.read(data, 4);
    if (BitConverter::readString((byte*)data, 4) != "PCK")
        return false;
    /* 4 - 7:  (4B) version */
    in.read(data, 4);
    /* 8 - 11: (4B) Number of files */
    in.read(data, 4);
    _numberOfFiles = BitConverter::readUInt((byte*)data, 4);
    delete[] data;
    return true;
}

void Package::writeFileIndex(ofstream& out) {
    unsigned int position = 3 * 4;
    char* buffer = new char[4];
    for (FILE_LIST::iterator i = _fileList.begin(); i != _fileList.end(); i++) {
        position += (*i)->getHeaderSize() + 4;
    }
    for (FILE_LIST::iterator i = _fileList.begin(); i != _fileList.end(); i++) {
        (*i)->writeHeader(out);
        BitConverter::writeUInt((byte*)buffer,position,4);
        out.write(buffer, 4);
        position += (*i)->getFileLength();
    }
    delete[] buffer;
}

void Package::writeData(ofstream& out) {
    for (FILE_LIST::iterator i = _fileList.begin(); i != _fileList.end(); i++) {
        (*i)->writeFileData(out);
    }
}

string Package::generateKey() {
    std::stringstream ss;
    string temp;
    char range = 'Z' - 'A';
    srand(time(NULL));
    while (true) {
        ss.clear();
        for (int i = 0; i < 4; i++) {
            ss << (char) ('A' + rand() % range);
        }
        ss >> temp;
        if (_fileDict.find(temp) == _fileDict.end())
            return temp;
    }
}
