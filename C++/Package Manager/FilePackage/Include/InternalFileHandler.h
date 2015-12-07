/* 
 * File:   InternalFileHandler.h
 * Author: mato
 *
 * Created on Sobota, 2013, december 14, 11:54
 */

#ifndef INTERNALFILEHANDLER_H
#define	INTERNALFILEHANDLER_H

#include "FileHandler.h"
#include "BitConverter.h"
#include <stdio.h>

class InternalFileHandler : public FileHandler {
    /*Properties*/
private:
    string _filename;
    string _tempDir;
    string _source;
    string _path;
    unsigned int _dataType;
    unsigned int _fileSize;
    unsigned int _fileOffset;
    bool _extracted;
    bool _valid;
    /*Constructor and destructor*/
public:
    InternalFileHandler(string source, ifstream& in);
    InternalFileHandler(string source, unsigned int offset);
    virtual ~InternalFileHandler();

private:
    void readHeaderInfo(ifstream& in);

    /*Reimplemented abstract methods*/
public:
    virtual string getFileName(); //returns name of the file
    virtual unsigned int getDataType(); //returns data type of the file
    virtual unsigned int getFileLength();

    virtual void writeFileData(ofstream&);
    virtual string getFilePath();
    virtual bool isValid();
    /*Public interface*/
public:
    void setTempDirectory(string dir);
    
};

#endif	/* INTERNALFILEHANDLER_H */

