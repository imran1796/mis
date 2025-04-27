function columnNumberToName(num) {
    let columnName = '';
    while (num > 0) {
        let remainder = (num - 1) % 26;
        columnName = String.fromCharCode(65 + remainder) + columnName;
        num = Math.floor((num - 1) / 26);
    }
    return columnName;
}

// Process thead, tbody, and tfoot
function processHtmlTableToExcelJs(table, worksheet) {
    const cellMap = {}; // Track occupied cells (row,col)
    let currentRowIndex = 1;

    const sections = ['thead', 'tbody', 'tfoot'];

    sections.forEach(section => {
        const rows = table.querySelectorAll(`${section} tr`);

        rows.forEach((row) => {
            const cells = Array.from(row.children);
            let currentColIndex = 1;

            const rowData = []; // for normal cells

            cells.forEach((cell) => {
                // Skip already occupied cells (because of rowspan/colspan)
                while (cellMap[`${currentRowIndex},${currentColIndex}`]) {
                    currentColIndex++;
                }

                const colspan = parseInt(cell.getAttribute("colspan") || 1);
                const rowspan = parseInt(cell.getAttribute("rowspan") || 1);

                const startCol = currentColIndex;
                const endCol = currentColIndex + colspan - 1;
                const startRow = currentRowIndex;
                const endRow = currentRowIndex + rowspan - 1;

                // Merge cells if colspan or rowspan > 1
                if (colspan > 1 || rowspan > 1) {
                    const startCell = columnNumberToName(startCol) + startRow;
                    const endCell = columnNumberToName(endCol) + endRow;
                    worksheet.mergeCells(`${startCell}:${endCell}`);
                }

                const excelCell = worksheet.getCell(columnNumberToName(
                    startCol) + startRow);
                excelCell.value = cell.textContent.trim();
                excelCell.alignment = {
                    vertical: 'middle',
                    horizontal: 'center',
                    wrapText: true
                };
                excelCell.border = {
                    top: {
                        style: 'thin'
                    },
                    left: {
                        style: 'thin'
                    },
                    bottom: {
                        style: 'thin'
                    },
                    right: {
                        style: 'thin'
                    }
                };
                if (section !== 'tbody') {
                    excelCell.font = {
                        bold: true
                    };
                }

                // Mark occupied cells
                for (let r = startRow; r <= endRow; r++) {
                    for (let c = startCol; c <= endCol; c++) {
                        cellMap[`${r},${c}`] = true;
                    }
                }

                currentColIndex += colspan;
            });

            currentRowIndex++;
        });
    });
}

$('#btnExport').on('click', async function() {
    try {
        const reportTitle = $('.reportTitle').text().trim().replace(/\s+/g, '_'); 
        const reportRange = $('.reportRange').text().trim().replace(/\s+/g, '_');

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet("Export Report");

        const table = document.getElementById("excelJsTable");

        if (!table) {
            throw new Error("Table not found");
        }

        processHtmlTableToExcelJs(table, worksheet);

        // Auto-fit columns width (with max width limit)
        worksheet.columns.forEach((column) => {
            let maxLength = 0;
            column.eachCell({
                includeEmpty: true
            }, (cell) => {
                const value = cell.value ? cell.value.toString() : "";
                maxLength = Math.max(maxLength, value.length);
            });
            column.width = Math.min(maxLength + 2, 15);
        });

        // Download Excel
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        });

        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `${reportTitle}_${reportRange}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

    } catch (error) {
        console.error("Export failed:", error);
        alert("Failed to export Excel: " + error.message);
    }
});