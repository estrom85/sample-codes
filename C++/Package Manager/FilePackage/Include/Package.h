/* 
 * File:   PackageHeader.h
 * Author: mato
 *
 * Created on Sobota, 2013, december 7, 19:59
 * 
 * Package header:
 * 0 - 3:  (4B) "PCK"
 * 4 - 7:  (4B) version
 * 8 - 11: (4B) Number of files
 * Index chunk
 * 12 - 15:(4B) "IDX"
 * 12 - 12+n1: (n1B) Header1
 * n1 - n1+3:  (4B) File1 position
 * ...
 * Data chunk
 * 15+sum(sizeof(headern)+4) - +3: (4B) "DAT"
 * ... data ...
 */

#ifndef PACKAGEHEADER_H
#define	PACKAGEHEADER_H

#include <string>
#include <map>
#include <vector>
#include <set>
#include <stdlib.h>
#include <sstream>
#include <time.h>
#include "FileHandler.h"
#include "ExternalFileHandler.h"
#include "InternalFileHandler.h"

using namespace std;
typedef map<string, FileHandler*> FILE_MAP;
typedef vector<FileHandler*> FILE_LIST;
typedef set<string> FILE_INDEX;

class Package {
public:
    enum{
        ERR_PACK_LOAD = 0x1,
        ERR_PACK_SAVE = 0x2,
        ERR_PACK_INVALID_PATH = 0x4,
        ERR_PACK_INVALID_TEMP_DIR = 0x10,
        ERR_FILE_CANT_OPEN = 0x100,
        ERR_FILE_CANT_READ = 0x200
    };
    
    Package(char* tempDir = "");  
    Package(string path,char* tempDir="");
    virtual ~Package();
    
    bool LoadFile(string path);
    bool LoadFile(string key, string path);
    void ChangeKey(string oldKey, string newKey);
    void RemoveFile(string key);
    
    FileHandler* operator[](string& key);
    FileHandler* operator[](const char* key);
    FileHandler* operator[](int key);
    
    FileHandler* getFile(string& key);
    FileHandler* getFile(const char* key);
    FileHandler* getFile(int key);
    
    bool SavePackage(string path);
    const string& getFileName();
    int NumberOfFiles();
    
private:
    void writeHeader(ofstream&);
    void writeFileIndex(ofstream&);
    void writeData(ofstream&);
    
    bool readHeader(ifstream&);
    void readFileIndex(ifstream&);
    
    string generateKey();
    
    void addFileHandler(FileHandler*);
    
    int _numberOfFiles;
    char* _path;
    string _filename;
    string _tempdir;
    
    FILE_MAP _fileDict;
    FILE_LIST _fileList;
    FILE_INDEX _fileIndex;
};

#endif	/* PACKAGEHEADER_H */

